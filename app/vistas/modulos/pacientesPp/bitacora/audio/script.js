const init = () => {
        const tieneSoporteUserMedia = () =>
            !!(navigator.mediaDevices.getUserMedia)

        // Si no soporta...
        // Amable aviso para que el mundo comience a usar navegadores decentes ;)
        if (typeof MediaRecorder === "undefined" || !tieneSoporteUserMedia())
            return alert("Tu navegador web no cumple los requisitos; por favor, actualiza a un navegador decente como Firefox o Google Chrome");


        // Declaración de elementos del DOM
        const $listaDeDispositivos = document.querySelector("#listaDeDispositivos"),
            $duracion = document.querySelector("#duracion"),
            $btnComenzarGrabacion = document.querySelector("#btnComenzarGrabacion"),
            $btnDetenerGrabacion = document.querySelector("#btnDetenerGrabacion");

        // Algunas funciones útiles
        const limpiarSelect = () => {
            for (let x = $listaDeDispositivos.options.length - 1; x >= 0; x--) {
                $listaDeDispositivos.options.remove(x);
            }
        }

        const segundosATiempo = numeroDeSegundos => {
            let horas = Math.floor(numeroDeSegundos / 60 / 60);
            numeroDeSegundos -= horas * 60 * 60;
            let minutos = Math.floor(numeroDeSegundos / 60);
            numeroDeSegundos -= minutos * 60;
            numeroDeSegundos = parseInt(numeroDeSegundos);
            if (horas < 10) horas = "0" + horas;
            if (minutos < 10) minutos = "0" + minutos;
            if (numeroDeSegundos < 10) numeroDeSegundos = "0" + numeroDeSegundos;

            return `${horas}:${minutos}:${numeroDeSegundos}`;
        };
        // Variables "globales"
        let tiempoInicio, mediaRecorder, idIntervalo;
        const refrescar = () => {
                $duracion.textContent = segundosATiempo((Date.now() - tiempoInicio) / 1000);
            }
            // Consulta la lista de dispositivos de entrada de audio y llena el select
        const llenarLista = () => {
            navigator
                .mediaDevices
                .enumerateDevices()
                .then(dispositivos => {
                    limpiarSelect();
                    dispositivos.forEach((dispositivo, indice) => {
                        if (dispositivo.kind === "audioinput") {
                            const $opcion = document.createElement("option");
                            // Firefox no trae nada con label, que viva la privacidad
                            // y que muera la compatibilidad
                            $opcion.text = dispositivo.label || `Dispositivo ${indice + 1}`;
                            $opcion.value = dispositivo.deviceId;
                            $listaDeDispositivos.appendChild($opcion);
                        }
                    })
                })
        };
        // Ayudante para la duración; no ayuda en nada pero muestra algo informativo
        const comenzarAContar = () => {
            tiempoInicio = Date.now();
            idIntervalo = setInterval(refrescar, 500);
        };

        // Comienza a grabar el audio con el dispositivo seleccionado
        const comenzarAGrabar = () => {
            if (!$listaDeDispositivos.options.length) return alert("No hay dispositivos");
            // No permitir que se grabe doblemente
            if (mediaRecorder) return alert("Ya se está grabando");

            navigator.mediaDevices.getUserMedia({
                    audio: {
                        deviceId: $listaDeDispositivos.value,
                    }
                })
                .then(stream => {
                    // Comenzar a grabar con el stream
                    mediaRecorder = new MediaRecorder(stream);
                    mediaRecorder.start();
                    comenzarAContar();
                    // En el arreglo pondremos los datos que traiga el evento dataavailable
                    const fragmentosDeAudio = [];
                    // Escuchar cuando haya datos disponibles
                    mediaRecorder.addEventListener("dataavailable", evento => {
                        // Y agregarlos a los fragmentos
                        fragmentosDeAudio.push(evento.data);
                    });
                    // Cuando se detenga (haciendo click en el botón) se ejecuta esto
                    mediaRecorder.addEventListener("stop", () => {
                        // Detener el stream
                        stream.getTracks().forEach(track => track.stop());
                        // Detener la cuenta regresiva
                        detenerConteo();
                        // Convertir los fragmentos a un objeto binario
                        const blobAudio = new Blob(fragmentosDeAudio);
                        const formData = new FormData();
                        // Enviar el BinaryLargeObject con FormData
                        formData.append("audio", blobAudio);
                        const RUTA_SERVIDOR = "guardar.php";
                        $duracion.textContent = "Enviando audio...";
                        fetch(RUTA_SERVIDOR, {
                                method: "POST",
                                body: formData,
                            })
                            .then(respuestaRaw => respuestaRaw.text()) // Decodificar como texto
                            .then(respuestaComoTexto => {
                                // Aquí haz algo con la respuesta ;)
                                console.log("La respuesta: ", respuestaComoTexto);
                                // Abrir el archivo, es opcional y solo lo pongo como demostración
                                $duracion.innerHTML = `<strong>Audio subido correctamente.</strong>&nbsp; <a target="_blank" href="${respuestaComoTexto}">Abrir</a>`
                            })
                    });
                })
                .catch(error => {
                    // Aquí maneja el error, tal vez no dieron permiso
                    console.log(error)
                });
        };


        const detenerConteo = () => {
            clearInterval(idIntervalo);
            tiempoInicio = null;
            $duracion.textContent = "";
        }

        const detenerGrabacion = () => {
            if (!mediaRecorder) return alert("No se está grabando");
            mediaRecorder.stop();
            mediaRecorder = null;
        };


        $btnComenzarGrabacion.addEventListener("click", comenzarAGrabar);
        $btnDetenerGrabacion.addEventListener("click", detenerGrabacion);

        // Cuando ya hemos configurado lo necesario allá arriba llenamos la lista

        llenarLista();
    }
    // Esperar a que el documento esté listo...
document.addEventListener("DOMContentLoaded", init);