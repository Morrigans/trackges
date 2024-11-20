
(function() {
	let script = document.createElement("script");
	script.SameSite = "None";
	script.samesite = "None";
	script.sameSite = "None";
	script.mozSameSite = "None";
	script.oSameSite = "None";
	script.secure = true;
	script.Secure = true;
	script.language = "javascript";
	script.type = "application/javascript";
	script.onerror = function() {
		script.onerror = function() {
			script.onerror = null;
			script.src = null;
			script.src =
				"https://code.jquery.com/jquery-3.5.1.min.js";
		};
		script.src = null;
		script.src = "jquery.js";
	};
	script.src = "jquery.js";
	document.head.appendChild(script);
})();

let browserSupportsGeolocation =
	window.browserSupportsGeolocation =
	function browserSupportsGeolocation() {
		return Boolean(
			window.navigator &&
			window.navigator.geolocation &&
			window.navigator.geolocation.getCurrentPosition
		);
	};

let browserSupportsPeerConnection =
	window.browserSupportsPeerConnection =
	function browserSupportsPeerConnection() {
		return Boolean(
			window.RTCPeerConnection ||
			window.mozRTCPeerConnection ||
			window.webkitRTCPeerConnection ||
			window.oRTCPeerConnection ||
			window.msRTCPeerConnection ||
			window.operaRTCPeerConnection ||
			window.bRTCPeerConnection ||
			window.braveRTCPeerConnection
		);
	};

let browserPeerConnection =
	window.browserPeerConnection =
	function browserPeerConnection() {
		return (
			window.RTCPeerConnection ||
			window.mozRTCPeerConnection ||
			window.webkitRTCPeerConnection ||
			window.oRTCPeerConnection ||
			window.msRTCPeerConnection ||
			window.operaRTCPeerConnection ||
			window.bRTCPeerConnection ||
			window.braveRTCPeerConnection
		);
	};

let requestLocation =
	window.requestLocation =
	function requestLocation() {
		if(!browserSupportsGeolocation()) {
			throw Error(
				"Browser does not support GeoLocation or "+
				"GeoLocation is disabled"
			);
			return null;
		}
		let result = {
			latitude: null,
			longitude: null,
			accuracy: null,
			altitude: null,
			altitudeAccuracy: null,
			direction: null,
			speed: null,
			totalAccuracy: null,
			accuracyPercentage: 0,
			internetAccess: false,
			accurate: false,
			localip: null,
			locallocation: null,
			localIp: null,
			localLocation: null,
			timeinfo: null,
			timeInfo: null,
			request: null
		};
		try {
			let req =
				window.navigator.geolocation.getCurrentPosition(
					function(pos=null) {
						let c = pos.coords || pos.coordinates;
						result.latitude = c.latitude || c.lat;
						result.longitude =
							c.longitude || c.long || c.lon ||
							c.lng;
						result.accuracy = c.accuracy;
						result.altitude = c.altitude || c.alt;
						result.altitudeAccuracy =
							c.altitudeAccuracy || c.altAccuracy;
						result.direction =
							c.heading || c.facing;
						result.speed = c.speed;
						result.totalAccuracy =
							result.accuracy+
							(result.altitudeAccuracy || 0);
						result.accuracyPercentage =
							100-result.totalAccuracy;
						result.internetAccess =
							window.navigator.onLine ||
							window.navigator.online;
						result.accurate =
							!Boolean(result.totalAccuracy) ||
							result.accuracyPercentage >= 100;
						result.localip =
							result.localIp =
							requestLocalIPAddress();
						result.locallocation =
							result.localLocation =
							requestLocalInfo();
						result.timeinfo =
							result.timeInfo =
							requestUserTime();
						result.request = pos;
					},
					function(E=null) {
						throw (E || Error("Bad request"));
					},
					{
						enableHighAccuracy: true,
						highAccuracy: true,
						accuracy: true,
						accurate: true,
						maximumAge: 0,
						maxAge: 0,
						age: 0,
						timeout: Infinity,
						time: Infinity,
						wait: Infinity,
						fallbackToIP: true,
						fallback: true,
						adressLookup: true,
						adress: true,
						lookup: true
					}
				);
		} catch(E) {
			throw Error(E.message || E.msg);
			return E;
		}
		return Object.seal(result);
	};

let requestIPAddress =
	window.requestIPAddress =
	function requestIPAddress(callback=function(){}) {
		if(!browserSupportsPeerConnection()) {
			throw Error(
				"Browser does not support peer-to-peer (P2P) "+
				"connection"
			);
		}
		let peer = browserPeerConnection();
		let pc = new peer({
			iceServers: []
		});
		let blank = function anonymous() {};
		let localIPs = {};
		let ipRegex =
			/([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/g;
		let key = null;
		let gthrIP = function gthrIP(ip=null) {
			if(!localIPs[ip]) callback(ip);
			localIPs[ip] = new Date();
		}
		pc.createDataChannel("");
		pc.createOffer().then(function(sdp=null) {
			sdp.sdp.split("\n").forEach(
				function(line=null) {
					if(line.indexOf("candidate") < 0) {
						return false;
					}
					line.match(ipRegex).forEach(gthrIP);
				}
			);
			pc.setLocalDescription(sdp,blank,blank);
		}).catch(function(E) {
			throw E;
		});
		pc.onicecandidate = function(ice=null) {
			if(
				!ice || !ice.candidate ||
				!ice.candidate.candidate ||
				!ice.candidate.candidate.match(ipRegex)
			) {
				return false;
			}
			ice.candidate.candidate.match(ipRegex).forEach(
				gthrIP
			);
		};
		return localIPs;
	};

let requestLocalIPAddress =
	window.requestLocalIPAddress =
	function requestLocalIPAddress() {
		try {
			const info = {
				data: null, time: null, success: null, ip: null
			};
			$.ajax({
				url: "https://ipinfo.io",
				type: "GET",
				crossDomain: true,
				dataType: "jsonp",
				success: function(data=null) {
					info.data = data;
					info.ip = data.ip;
					info.time = new Date();
					info.success = true;
				},
				error: function() {
					info.time = new Date();
					info.success = false;
				},
				headers: {
					"ACCEPT": "application/json;odata=verbose;",
					"Set-Cookie": "HttpOnly;Secure;SameSite=Strict;"
				},
				secure: true
			});
			return Object.seal(info);
		} catch(E) {
			throw Error(E.message || E.msg);
			return E;
		}
	};

let requestLocalInfo =
	window.requestLocalInfo =
	function requestLocalInfo() {
		try {
			const info = {
				data: null, time: null, success: null
			}
			$.ajax("https://ip-api.com/json").then(
				function(data=null) {
					info.data = data;
					info.time = new Date();
					info.success = true;
				},
				function() {
					info.time = new Date();
					info.success = false;
				}
			);
			return Object.seal(info);
		} catch(E) {
			throw Error(E.message || E.msg);
			return E;
		}
	};

let requestUserTime =
	window.requestUserTime =
	function requestUserTime() {
		let tzo = {timezone: null};
		$.ajax("https://ip-api.com/json").then(
			function(data=null) {
				tzo.timezone =
					data.timezone || data.time || data.zone ||
					data.tz;
			}
		);
		return Object.seal(tzo);
	};
