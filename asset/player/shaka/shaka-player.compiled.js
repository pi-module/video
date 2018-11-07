(function () {
    var g = {};
    (function (window) {
        var h, aa = this;
        aa.Uc = !0;

        function m(a, b) {
            var c = a.split("."), d = aa;
            c[0] in d || !d.execScript || d.execScript("var " + c[0]);
            for (var e; c.length && (e = c.shift());) c.length || void 0 === b ? d[e] ? d = d[e] : d = d[e] = {} : d[e] = b
        }

        function ba(a) {
            var b = n;

            function c() {
            }

            c.prototype = b.prototype;
            a.Xc = b.prototype;
            a.prototype = new c;
            a.prototype.constructor = a;
            a.Vc = function (a, c, f) {
                return b.prototype[c].apply(a, Array.prototype.slice.call(arguments, 2))
            }
        };

        /*

         Copyright 2016 Google Inc.

         Licensed under the Apache License, Version 2.0 (the "License");
         you may not use this file except in compliance with the License.
         You may obtain a copy of the License at

         http://www.apache.org/licenses/LICENSE-2.0

         Unless required by applicable law or agreed to in writing, software
         distributed under the License is distributed on an "AS IS" BASIS,
         WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
         See the License for the specific language governing permissions and
         limitations under the License.
         */
        function ca(a) {
            this.c = Math.exp(Math.log(.5) / a);
            this.a = this.b = 0
        }

        function da(a, b, c) {
            var d = Math.pow(a.c, b);
            a.b = c * (1 - d) + d * a.b;
            a.a += b
        }

        function ea(a) {
            return a.b / (1 - Math.pow(a.c, a.a))
        };

        function fa() {
            this.a = new ca(3);
            this.c = new ca(10);
            this.b = 5E5
        }

        function ga(a) {
            return .5 > a.a.a ? a.b : Math.min(ea(a.a), ea(a.c))
        };

        function ha() {
        }

        function ia() {
        };

        function ja() {
            this.h = null;
            this.f = !1;
            this.c = new fa;
            this.g = {};
            this.a = {};
            this.i = !1;
            this.b = null
        }

        m("shaka.abr.SimpleAbrManager", ja);
        h = ja.prototype;
        h.stop = function () {
            this.h = null;
            this.f = !1;
            this.g = {};
            this.a = {};
            this.b = null
        };
        h.init = function (a) {
            this.h = a
        };
        h.chooseStreams = function (a) {
            for (var b in a) this.g[b] = a[b];
            b = {};
            if ("audio" in a) {
                var c = ka(this);
                c ? (b.audio = c, this.a.audio = c) : delete this.a.audio
            }
            "video" in a && ((c = la(this)) ? (b.video = c, this.a.video = c) : delete this.a.video);
            "text" in a && (b.text = a.text.streams[0]);
            this.b = Date.now();
            return b
        };
        h.enable = function () {
            this.f = !0
        };
        h.disable = function () {
            this.f = !1
        };
        h.segmentDownloaded = function (a, b, c) {
            var d = this.c;
            a = b - a;
            65536 > c || (a = Math.max(a, 50), c = 8E3 * c / a, a /= 1E3, da(d.a, a, c), da(d.c, a, c));
            if (null != this.b && this.f) a:{
                d = Date.now() - this.b;
                if (!this.i) {
                    if (4E3 > d) break a;
                    this.i = !0
                } else if (8E3 > d) break a;
                d = this.a.video;
                c = {};
                if (a = ka(this)) c.audio = a, this.a.audio = a;
                if (a = la(this)) c.video = a, this.a.video = a;
                this.b = Date.now();
                a = void 0;
                d && c.video && c.video.bandwidth > d.bandwidth && (a = 10);
                this.h(c, a)
            }
        };
        h.getBandwidthEstimate = function () {
            return ga(this.c)
        };
        h.setDefaultEstimate = function (a) {
            this.c.b = a
        };

        function ka(a) {
            a = a.g.audio;
            if (!a) return null;
            a = ma(a);
            return a[Math.floor(a.length / 2)]
        }

        function la(a) {
            var b = a.g.video;
            if (!b) return null;
            var b = ma(b), c = a.a.audio, c = c && c.bandwidth || 0;
            a = ga(a.c);
            for (var d = b[0], e = 0; e < b.length; ++e) {
                var f = b[e], g = e + 1 < b.length ? b[e + 1] : {bandwidth: Number.POSITIVE_INFINITY};
                f.bandwidth && (g = (g.bandwidth + c) / .85, a >= (f.bandwidth + c) / .95 && a <= g && (d = f))
            }
            return d
        }

        function ma(a) {
            return a.streams.slice(0).filter(function (a) {
                return a.allowedByApplication && a.allowedByKeySystem
            }).sort(function (a, c) {
                return a.bandwidth - c.bandwidth
            })
        };
        var na = "ended play playing pause pausing ratechange seeked seeking timeupdate volumechange".split(" "),
            oa = "buffered currentTime duration ended loop muted paused playbackRate seeking videoHeight videoWidth volume".split(" "),
            pa = ["loop", "playbackRate"], qa = ["pause", "play"],
            ra = ["adaptation", "buffering", "error", "texttrackvisibility", "trackschanged"],
            sa = "getConfiguration getManifestUri getPlaybackRate getTracks getStats isBuffering isLive isTextTrackVisible seekRange".split(" "),
            ta = [["getConfiguration",
                "configure"]], ua = [["isTextTrackVisible", "setTextTrackVisibility"]],
            va = "configure resetConfiguration trickPlay cancelTrickPlay selectTrack setTextTrackVisibility addTextTrack".split(" "),
            wa = ["load", "unload"];

        function xa(a) {
            return JSON.stringify(a, function (a, c) {
                if ("manager" != a && "function" != typeof c) {
                    if (c instanceof Event || c instanceof q) {
                        var d = {}, e;
                        for (e in c) {
                            var f = c[e];
                            f && "object" == typeof f || e in Event || (d[e] = f)
                        }
                        return d
                    }
                    if (c instanceof TimeRanges) for (d = {
                        __type__: "TimeRanges",
                        length: c.length,
                        start: [],
                        end: []
                    }, e = 0; e < c.length; ++e) d.start.push(c.start(e)), d.end.push(c.end(e)); else d = "number" == typeof c ? isNaN(c) ? "NaN" : isFinite(c) ? c : 0 > c ? "-Infinity" : "Infinity" : c;
                    return d
                }
            })
        }

        function ya(a) {
            return JSON.parse(a, function (a, c) {
                return "NaN" == c ? NaN : "-Infinity" == c ? -Infinity : "Infinity" == c ? Infinity : c && "object" == typeof c && "TimeRanges" == c.__type__ ? za(c) : c
            })
        }

        function za(a) {
            return {
                length: a.length, start: function (b) {
                    return a.start[b]
                }, end: function (b) {
                    return a.end[b]
                }
            }
        };

        function t(a, b, c) {
            this.category = a;
            this.code = b;
            this.data = Array.prototype.slice.call(arguments, 2)
        }

        m("shaka.util.Error", t);
        t.prototype.toString = function () {
            return "shaka.util.Error " + JSON.stringify(this, null, "  ")
        };
        t.Category = {NETWORK: 1, TEXT: 2, MEDIA: 3, MANIFEST: 4, STREAMING: 5, DRM: 6, PLAYER: 7, CAST: 8, STORAGE: 9};
        t.Code = {
            UNSUPPORTED_SCHEME: 1E3,
            BAD_HTTP_STATUS: 1001,
            HTTP_ERROR: 1002,
            TIMEOUT: 1003,
            MALFORMED_DATA_URI: 1004,
            UNKNOWN_DATA_URI_ENCODING: 1005,
            INVALID_TEXT_HEADER: 2E3,
            INVALID_TEXT_CUE: 2001,
            INVALID_TEXT_SETTINGS: 2002,
            UNABLE_TO_DETECT_ENCODING: 2003,
            BAD_ENCODING: 2004,
            INVALID_XML: 2005,
            INVALID_TTML: 2006,
            INVALID_MP4_TTML: 2007,
            INVALID_MP4_VTT: 2008,
            BUFFER_READ_OUT_OF_BOUNDS: 3E3,
            JS_INTEGER_OVERFLOW: 3001,
            EBML_OVERFLOW: 3002,
            EBML_BAD_FLOATING_POINT_SIZE: 3003,
            MP4_SIDX_WRONG_BOX_TYPE: 3004,
            MP4_SIDX_INVALID_TIMESCALE: 3005,
            MP4_SIDX_TYPE_NOT_SUPPORTED: 3006,
            WEBM_CUES_ELEMENT_MISSING: 3007,
            WEBM_EBML_HEADER_ELEMENT_MISSING: 3008,
            WEBM_SEGMENT_ELEMENT_MISSING: 3009,
            WEBM_INFO_ELEMENT_MISSING: 3010,
            WEBM_DURATION_ELEMENT_MISSING: 3011,
            WEBM_CUE_TRACK_POSITIONS_ELEMENT_MISSING: 3012,
            WEBM_CUE_TIME_ELEMENT_MISSING: 3013,
            MEDIA_SOURCE_OPERATION_FAILED: 3014,
            MEDIA_SOURCE_OPERATION_THREW: 3015,
            VIDEO_ERROR: 3016,
            QUOTA_EXCEEDED_ERROR: 3017,
            UNABLE_TO_GUESS_MANIFEST_TYPE: 4E3,
            DASH_INVALID_XML: 4001,
            DASH_NO_SEGMENT_INFO: 4002,
            DASH_EMPTY_ADAPTATION_SET: 4003,
            DASH_EMPTY_PERIOD: 4004,
            DASH_WEBM_MISSING_INIT: 4005,
            DASH_UNSUPPORTED_CONTAINER: 4006,
            DASH_PSSH_BAD_ENCODING: 4007,
            DASH_NO_COMMON_KEY_SYSTEM: 4008,
            DASH_MULTIPLE_KEY_IDS_NOT_SUPPORTED: 4009,
            DASH_CONFLICTING_KEY_IDS: 4010,
            UNPLAYABLE_PERIOD: 4011,
            RESTRICTIONS_CANNOT_BE_MET: 4012,
            INVALID_STREAMS_CHOSEN: 5005,
            NO_RECOGNIZED_KEY_SYSTEMS: 6E3,
            REQUESTED_KEY_SYSTEM_CONFIG_UNAVAILABLE: 6001,
            FAILED_TO_CREATE_CDM: 6002,
            FAILED_TO_ATTACH_TO_VIDEO: 6003,
            INVALID_SERVER_CERTIFICATE: 6004,
            FAILED_TO_CREATE_SESSION: 6005,
            FAILED_TO_GENERATE_LICENSE_REQUEST: 6006,
            LICENSE_REQUEST_FAILED: 6007,
            LICENSE_RESPONSE_REJECTED: 6008,
            ENCRYPTED_CONTENT_WITHOUT_DRM_INFO: 6010,
            NO_LICENSE_SERVER_GIVEN: 6012,
            OFFLINE_SESSION_REMOVED: 6013,
            EXPIRED: 6014,
            LOAD_INTERRUPTED: 7E3,
            CAST_API_UNAVAILABLE: 8E3,
            NO_CAST_RECEIVERS: 8001,
            ALREADY_CASTING: 8002,
            UNEXPECTED_CAST_ERROR: 8003,
            CAST_CANCELED_BY_USER: 8004,
            CAST_CONNECTION_TIMED_OUT: 8005,
            CAST_RECEIVER_APP_UNAVAILABLE: 8006,
            INDEXED_DB_NOT_SUPPORTED: 9E3,
            INDEXED_DB_ERROR: 9001,
            OPERATION_ABORTED: 9002,
            REQUESTED_ITEM_NOT_FOUND: 9003,
            MALFORMED_OFFLINE_URI: 9004,
            CANNOT_STORE_LIVE_OFFLINE: 9005,
            STORE_ALREADY_IN_PROGRESS: 9006,
            NO_INIT_DATA_FOR_OFFLINE: 9007
        };

        function q(a, b) {
            var c = b || {}, d;
            for (d in c) this[d] = c[d];
            this.defaultPrevented = this.cancelable = this.bubbles = !1;
            this.timeStamp = window.performance ? window.performance.now() : Date.now();
            this.type = a;
            this.isTrusted = !1;
            this.target = this.currentTarget = null;
            this.a = !1
        }

        q.prototype.preventDefault = function () {
        };
        q.prototype.stopImmediatePropagation = function () {
            this.a = !0
        };
        q.prototype.stopPropagation = function () {
        };

        function v() {
            var a, b, c = new Promise(function (c, e) {
                a = c;
                b = e
            });
            c.resolve = a;
            c.reject = b;
            return c
        };

        function Aa(a, b, c, d) {
            this.B = a;
            this.l = b;
            this.v = c;
            this.w = d;
            this.f = this.j = this.h = !1;
            this.u = "";
            this.a = this.i = null;
            this.b = {video: {}, player: {}};
            this.m = 0;
            this.c = {};
            this.g = null
        }

        h = Aa.prototype;
        h.o = function () {
            this.disconnect();
            this.w = this.v = this.l = null;
            this.f = this.j = this.h = !1;
            this.g = this.c = this.b = this.a = this.i = null;
            return Promise.resolve()
        };
        h.N = function () {
            return this.f
        };
        h.Ua = function () {
            return this.u
        };
        h.init = function () {
            if (window.chrome && chrome.cast && chrome.cast.isAvailable) {
                delete window.__onGCastApiAvailable;
                this.h = !0;
                this.l();
                var a = new chrome.cast.SessionRequest(this.B),
                    a = new chrome.cast.ApiConfig(a, this.Ub.bind(this), this.ac.bind(this), "origin_scoped");
                chrome.cast.initialize(a, function () {
                }, function () {
                })
            } else window.__onGCastApiAvailable = function (a) {
                a && this.init()
            }.bind(this)
        };
        h.Wa = function (a) {
            this.i = a;
            this.f && Ba(this, {type: "appData", appData: this.i})
        };
        h.cast = function (a) {
            if (!this.h) return Promise.reject(new t(8, 8E3));
            if (!this.j) return Promise.reject(new t(8, 8001));
            if (this.f) return Promise.reject(new t(8, 8002));
            this.g = new v;
            chrome.cast.requestSession(this.cc.bind(this, a), this.Sb.bind(this));
            return this.g
        };
        h.disconnect = function () {
            this.f && (Ca(this), this.a && (this.a.stop(function () {
            }, function () {
            }), this.a = null))
        };
        h.get = function (a, b) {
            if ("video" == a) {
                if (0 <= qa.indexOf(b)) return this.rb.bind(this, a, b)
            } else if ("player" == a) {
                if (0 <= va.indexOf(b)) return this.rb.bind(this, a, b);
                if (0 <= wa.indexOf(b)) return this.qc.bind(this, a, b);
                if (0 <= sa.indexOf(b)) return this.pb.bind(this, a, b)
            }
            return this.pb(a, b)
        };
        h.set = function (a, b, c) {
            this.b[a][b] = c;
            Ba(this, {type: "set", targetName: a, property: b, value: c})
        };
        h.cc = function (a, b) {
            Da(this, b);
            Ba(this, {type: "init", initState: a, appData: this.i});
            this.g.resolve()
        };
        h.Sb = function (a) {
            var b = 8003;
            switch (a.code) {
                case "cancel":
                    b = 8004;
                    break;
                case "timeout":
                    b = 8005;
                    break;
                case "receiver_unavailable":
                    b = 8006
            }
            this.g.reject(new t(8, b, a))
        };
        h.pb = function (a, b) {
            return this.b[a][b]
        };
        h.rb = function (a, b) {
            Ba(this, {type: "call", targetName: a, methodName: b, args: Array.prototype.slice.call(arguments, 2)})
        };
        h.qc = function (a, b) {
            var c = Array.prototype.slice.call(arguments, 2), d = new v, e = this.m.toString();
            this.m++;
            this.c[e] = d;
            Ba(this, {type: "asyncCall", targetName: a, methodName: b, args: c, id: e});
            return d
        };
        h.Ub = function (a) {
            Da(this, a)
        };
        h.ac = function (a) {
            this.j = "available" == a;
            this.l()
        };

        function Da(a, b) {
            a.a = b;
            a.a.addUpdateListener(a.ib.bind(a));
            a.a.addMessageListener("urn:x-cast:com.google.shaka.v2", a.Vb.bind(a));
            a.ib()
        }

        h.ib = function () {
            var a = this.a ? "connected" == this.a.status : !1;
            if (this.f && !a) {
                this.w();
                for (var b in this.b) this.b[b] = {};
                Ca(this)
            }
            this.u = (this.f = a) ? this.a.receiver.friendlyName : "";
            this.l()
        };

        function Ca(a) {
            for (var b in a.c) {
                var c = a.c[b];
                delete a.c[b];
                c.reject(new t(7, 7E3))
            }
        }

        h.Vb = function (a, b) {
            var c = ya(b);
            switch (c.type) {
                case "event":
                    var d = c.targetName, e = c.event;
                    this.v(d, new q(e.type, e));
                    break;
                case "update":
                    e = c.update;
                    for (d in e) {
                        var c = this.b[d] || {}, f;
                        for (f in e[d]) c[f] = e[d][f]
                    }
                    break;
                case "asyncComplete":
                    if (d = c.id, f = c.error, c = this.c[d], delete this.c[d], c) if (f) {
                        d = new t(f.category, f.code);
                        for (e in f) d[e] = f[e];
                        c.reject(d)
                    } else c.resolve()
            }
        };

        function Ba(a, b) {
            var c = xa(b);
            a.a.sendMessage("urn:x-cast:com.google.shaka.v2", c, function () {
            }, ha)
        };

        function Ea() {
            this.a = {}
        }

        h = Ea.prototype;
        h.push = function (a, b) {
            this.a.hasOwnProperty(a) ? this.a[a].push(b) : this.a[a] = [b]
        };
        h.set = function (a, b) {
            this.a[a] = b
        };
        h.has = function (a) {
            return this.a.hasOwnProperty(a)
        };
        h.get = function (a) {
            return (a = this.a[a]) ? a.slice() : null
        };
        h.remove = function (a, b) {
            var c = this.a[a];
            if (c) for (var d = 0; d < c.length; ++d) c[d] == b && (c.splice(d, 1), --d)
        };
        h.keys = function () {
            var a = [], b;
            for (b in this.a) a.push(b);
            return a
        };
        h.clear = function () {
            this.a = {}
        };

        function w() {
            this.a = new Ea
        }

        w.prototype.o = function () {
            Fa(this);
            this.a = null;
            return Promise.resolve()
        };

        function x(a, b, c, d) {
            b = new Ga(b, c, d);
            a.a.push(c, b)
        }

        w.prototype.ha = function (a, b) {
            for (var c = this.a.get(b) || [], d = 0; d < c.length; ++d) {
                var e = c[d];
                e.target == a && (e.ha(), this.a.remove(b, e))
            }
        };

        function Fa(a) {
            var b = a.a, c = [], d;
            for (d in b.a) c.push.apply(c, b.a[d]);
            for (b = 0; b < c.length; ++b) c[b].ha();
            a.a.clear()
        }

        function Ga(a, b, c) {
            this.target = a;
            this.type = b;
            this.a = c;
            this.target.addEventListener(b, c, !1)
        }

        Ga.prototype.ha = function () {
            this.target && (this.target.removeEventListener(this.type, this.a, !1), this.a = this.target = null)
        };

        function n() {
            this.ya = new Ea;
            this.W = this
        }

        n.prototype.addEventListener = function (a, b) {
            this.ya.push(a, b)
        };
        n.prototype.removeEventListener = function (a, b) {
            this.ya.remove(a, b)
        };
        n.prototype.dispatchEvent = function (a) {
            for (var b = this.ya.get(a.type) || [], c = 0; c < b.length; ++c) {
                a.target = this.W;
                a.currentTarget = this.W;
                var d = b[c];
                try {
                    d.handleEvent ? d.handleEvent(a) : d.call(this, a)
                } catch (e) {
                }
                if (a.a) break
            }
            return a.defaultPrevented
        };

        function y(a, b, c) {
            n.call(this);
            this.c = a;
            this.b = b;
            this.h = this.f = this.g = this.i = this.j = null;
            this.a = new Aa(c, this.Ec.bind(this), this.Fc.bind(this), this.Gc.bind(this));
            Ha(this)
        }

        ba(y);
        m("shaka.cast.CastProxy", y);
        y.prototype.o = function () {
            var a = [this.h ? this.h.o() : null, this.b ? this.b.o() : null, this.a ? this.a.o() : null];
            this.a = this.h = this.i = this.j = this.b = this.c = null;
            return Promise.all(a)
        };
        y.prototype.destroy = y.prototype.o;
        y.prototype.Lb = function () {
            return this.j
        };
        y.prototype.getVideo = y.prototype.Lb;
        y.prototype.Jb = function () {
            return this.i
        };
        y.prototype.getPlayer = y.prototype.Jb;
        y.prototype.Bb = function () {
            return this.a ? this.a.h && this.a.j : !1
        };
        y.prototype.canCast = y.prototype.Bb;
        y.prototype.N = function () {
            return this.a ? this.a.N() : !1
        };
        y.prototype.isCasting = y.prototype.N;
        y.prototype.Ua = function () {
            return this.a ? this.a.Ua() : ""
        };
        y.prototype.receiverName = y.prototype.Ua;
        y.prototype.cast = function () {
            var a = {video: {}, player: {}, playerAfterLoad: {}, manifest: this.b.la, startTime: null};
            this.c.pause();
            pa.forEach(function (b) {
                a.video[b] = this.c[b]
            }.bind(this));
            this.c.ended || (a.startTime = this.c.currentTime);
            ta.forEach(function (b) {
                var c = b[1];
                b = this.b[b[0]]();
                a.player[c] = b
            }.bind(this));
            ua.forEach(function (b) {
                var c = b[1];
                b = this.b[b[0]]();
                a.playerAfterLoad[c] = b
            }.bind(this));
            return this.a.cast(a).then(function () {
                return this.b.$a()
            }.bind(this))
        };
        y.prototype.cast = y.prototype.cast;
        y.prototype.Wa = function (a) {
            this.a.Wa(a)
        };
        y.prototype.setAppData = y.prototype.Wa;
        y.prototype.disconnect = function () {
            this.a.disconnect()
        };
        y.prototype.disconnect = y.prototype.disconnect;

        function Ha(a) {
            a.a.init();
            a.h = new w;
            na.forEach(function (a) {
                x(this.h, this.c, a, this.Sc.bind(this))
            }.bind(a));
            ra.forEach(function (a) {
                x(this.h, this.b, a, this.lc.bind(this))
            }.bind(a));
            a.j = {};
            for (var b in a.c) Object.defineProperty(a.j, b, {
                configurable: !1,
                enumerable: !0,
                get: a.Rc.bind(a, b),
                set: a.Tc.bind(a, b)
            });
            a.i = {};
            for (b in a.b) Object.defineProperty(a.i, b, {configurable: !1, enumerable: !0, get: a.kc.bind(a, b)});
            a.g = new n;
            a.g.W = a.j;
            a.f = new n;
            a.f.W = a.i
        }

        h = y.prototype;
        h.Ec = function () {
            this.dispatchEvent(new q("caststatuschanged"))
        };
        h.Gc = function () {
            ta.forEach(function (a) {
                var b = a[1];
                a = this.a.get("player", a[0])();
                this.b[b](a)
            }.bind(this));
            var a = this.a.get("player", "getManifestUri")(), b = this.a.get("video", "ended"), c = Promise.resolve(),
                d = this.c.autoplay, e = null;
            b || (e = this.a.get("video", "currentTime"));
            a && (this.c.autoplay = !1, c = this.b.load(a, e), c["catch"](function (a) {
                this.b.dispatchEvent(new q("error", {detail: a}))
            }.bind(this)));
            var f = {};
            pa.forEach(function (a) {
                f[a] = this.a.get("video", a)
            }.bind(this));
            c.then(function () {
                pa.forEach(function (a) {
                    this.c[a] =
                        f[a]
                }.bind(this));
                ua.forEach(function (a) {
                    var b = a[1];
                    a = this.a.get("player", a[0])();
                    this.b[b](a)
                }.bind(this));
                this.c.autoplay = d;
                a && this.c.play()
            }.bind(this))
        };
        h.Rc = function (a) {
            if ("addEventListener" == a) return this.g.addEventListener.bind(this.g);
            if ("removeEventListener" == a) return this.g.removeEventListener.bind(this.g);
            if (this.a.N() && !Object.keys(this.a.b.video).length) {
                var b = this.c[a];
                if ("function" != typeof b) return b
            }
            return this.a.N() ? this.a.get("video", a) : (b = this.c[a], "function" == typeof b && (b = b.bind(this.c)), b)
        };
        h.Tc = function (a, b) {
            this.a.N() ? this.a.set("video", a, b) : this.c[a] = b
        };
        h.Sc = function (a) {
            this.a.N() || this.g.dispatchEvent(new q(a.type, a))
        };
        h.kc = function (a) {
            return "addEventListener" == a ? this.f.addEventListener.bind(this.f) : "removeEventListener" == a ? this.f.removeEventListener.bind(this.f) : "getNetworkingEngine" == a ? this.b.gb.bind(this.b) : this.a.N() && !Object.keys(this.a.b.video).length && 0 <= sa.indexOf(a) || !this.a.N() ? (a = this.b[a], a.bind(this.b)) : this.a.get("player", a)
        };
        h.lc = function (a) {
            this.a.N() || this.f.dispatchEvent(a)
        };
        h.Fc = function (a, b) {
            this.a.N() && ("video" == a ? this.g.dispatchEvent(b) : "player" == a && this.f.dispatchEvent(b))
        };

        function z(a, b, c) {
            n.call(this);
            this.b = a;
            this.a = b;
            this.i = {video: a, player: b};
            this.j = c || function () {
            };
            this.h = !1;
            this.g = !0;
            this.c = this.f = null;
            Ia(this)
        }

        ba(z);
        m("shaka.cast.CastReceiver", z);
        z.prototype.Nb = function () {
            return this.h
        };
        z.prototype.isConnected = z.prototype.Nb;
        z.prototype.Ob = function () {
            return this.g
        };
        z.prototype.isIdle = z.prototype.Ob;
        z.prototype.o = function () {
            var a = this.a ? this.a.o() : Promise.resolve();
            null != this.c && window.clearTimeout(this.c);
            this.j = this.i = this.a = this.b = null;
            this.h = !1;
            this.g = !0;
            this.c = this.f = null;
            return a.then(function () {
                cast.receiver.CastReceiverManager.getInstance().stop()
            })
        };
        z.prototype.destroy = z.prototype.o;

        function Ia(a) {
            var b = cast.receiver.CastReceiverManager.getInstance();
            b.onSenderConnected = a.nb.bind(a);
            b.onSenderDisconnected = a.nb.bind(a);
            b.onSystemVolumeChanged = a.Hb.bind(a);
            a.f = b.getCastMessageBus("urn:x-cast:com.google.shaka.v2");
            a.f.onMessage = a.Wb.bind(a);
            b.start();
            na.forEach(function (a) {
                this.b.addEventListener(a, this.qb.bind(this, "video"))
            }.bind(a));
            ra.forEach(function (a) {
                this.a.addEventListener(a, this.qb.bind(this, "player"))
            }.bind(a));
            a.a.xb(1920, 1080);
            a.a.addEventListener("loading", function () {
                this.g =
                    !1;
                Ja(this)
            }.bind(a));
            a.a.addEventListener("unloading", function () {
                this.g = !0;
                Ja(this)
            }.bind(a));
            a.b.addEventListener("ended", function () {
                window.setTimeout(function () {
                    this.b && this.b.ended && (this.g = !0, Ja(this))
                }.bind(this), 5E3)
            }.bind(a))
        }

        h = z.prototype;
        h.nb = function () {
            this.h = !!cast.receiver.CastReceiverManager.getInstance().getSenders().length;
            Ja(this)
        };

        function Ja(a) {
            Promise.resolve().then(function () {
                this.dispatchEvent(new q("caststatuschanged"))
            }.bind(a))
        }

        function Ka(a, b, c) {
            for (var d in b.player) a.a[d](b.player[d]);
            a.j(c);
            c = Promise.resolve();
            var e = a.b.autoplay;
            b.manifest && (a.b.autoplay = !1, c = a.a.load(b.manifest, b.startTime), c["catch"](function (a) {
                this.a.dispatchEvent(new q("error", {detail: a}))
            }.bind(a)));
            c.then(function () {
                for (var a in b.video) {
                    var c = b.video[a];
                    this.b[a] = c
                }
                for (a in b.playerAfterLoad) c = b.playerAfterLoad[a], this.a[a](c);
                this.b.autoplay = e;
                b.manifest && this.b.play()
            }.bind(a))
        }

        h.qb = function (a, b) {
            this.Ta();
            La(this, {type: "event", targetName: a, event: b})
        };
        h.Ta = function () {
            null != this.c && window.clearTimeout(this.c);
            this.c = window.setTimeout(this.Ta.bind(this), 500);
            var a = {video: {}, player: {}};
            oa.forEach(function (b) {
                a.video[b] = this.b[b]
            }.bind(this));
            sa.forEach(function (b) {
                a.player[b] = this.a[b]()
            }.bind(this));
            var b = cast.receiver.CastReceiverManager.getInstance().getSystemVolume();
            b && (a.video.volume = b.level, a.video.muted = b.muted);
            La(this, {type: "update", update: a})
        };
        h.Hb = function () {
            var a = cast.receiver.CastReceiverManager.getInstance().getSystemVolume();
            a && La(this, {type: "update", update: {video: {volume: a.level, muted: a.muted}}});
            La(this, {type: "event", targetName: "video", event: {type: "volumechange"}})
        };
        h.Wb = function (a) {
            var b = ya(a.data);
            switch (b.type) {
                case "init":
                    Ka(this, b.initState, b.appData);
                    this.Ta();
                    break;
                case "appData":
                    this.j(b.appData);
                    break;
                case "set":
                    var c = b.targetName, d = b.property, e = b.value;
                    if ("video" == c) if (b = cast.receiver.CastReceiverManager.getInstance(), "volume" == d) {
                        b.setSystemVolumeLevel(e);
                        break
                    } else if ("muted" == d) {
                        b.setSystemVolumeMuted(e);
                        break
                    }
                    this.i[c][d] = e;
                    break;
                case "call":
                    c = b.targetName;
                    d = b.methodName;
                    e = b.args;
                    c = this.i[c];
                    c[d].apply(c, e);
                    break;
                case "asyncCall":
                    c = b.targetName,
                        d = b.methodName, e = b.args, b = b.id, a = a.senderId, c = this.i[c], c[d].apply(c, e).then(this.vb.bind(this, a, b, null), this.vb.bind(this, a, b))
            }
        };
        h.vb = function (a, b, c) {
            La(this, {type: "asyncComplete", id: b, error: c}, a)
        };

        function La(a, b, c) {
            a.h && (b = xa(b), c ? a.f.getCastChannel(c).send(b) : a.f.broadcast(b))
        };

        function Ma(a, b) {
            return a.reduce(function (a, b, e) {
                return b["catch"](a.bind(null, e))
            }.bind(null, b), Promise.reject())
        }

        function A(a, b) {
            return a.concat(b)
        }

        function B() {
        }

        function Na(a) {
            return null != a
        }

        function Oa(a) {
            return function (b) {
                return b != a
            }
        };

        function Pa(a) {
            return !a || !Object.keys(a).length
        }

        function C(a) {
            return Object.keys(a).map(function (b) {
                return a[b]
            })
        }

        function Qa(a, b) {
            return Object.keys(a).reduce(function (c, d) {
                c[d] = b(a[d], d);
                return c
            }, {})
        }

        function Ra(a, b) {
            return Object.keys(a).every(function (c) {
                return b(c, a[c])
            })
        };

        function Sa(a) {
            return window.btoa(String.fromCharCode.apply(null, a)).replace(/\+/g, "-").replace(/\//g, "_").replace(/=*$/, "")
        }

        function Ta(a) {
            a = window.atob(a.replace(/-/g, "+").replace(/_/g, "/"));
            for (var b = new Uint8Array(a.length), c = 0; c < a.length; ++c) b[c] = a.charCodeAt(c);
            return b
        }

        function Ua(a) {
            for (var b = new Uint8Array(a.length / 2),
                     c = 0; c < a.length; c += 2) b[c / 2] = window.parseInt(a.substr(c, 2), 16);
            return b
        }

        function Va(a) {
            for (var b = "", c = 0; c < a.length; ++c) {
                var d = a[c].toString(16);
                1 == d.length && (d = "0" + d);
                b += d
            }
            return b
        }

        function Wa(a, b) {
            if (!a && !b) return !0;
            if (!a || !b || a.length != b.length) return !1;
            for (var c = 0; c < a.length; ++c) if (a[c] != b[c]) return !1;
            return !0
        };

        function Xa(a, b) {
            var c = D(a, b);
            return 1 != c.length ? null : c[0]
        }

        function D(a, b) {
            return Array.prototype.filter.call(a.childNodes, function (a) {
                return a.tagName == b
            })
        }

        function Ya(a) {
            return (a = a.firstChild) && a.nodeType == Node.TEXT_NODE ? a.nodeValue.trim() : null
        }

        function F(a, b, c, d) {
            var e = null;
            a = a.getAttribute(b);
            null != a && (e = c(a));
            return null == e ? void 0 !== d ? d : null : e
        }

        function $a(a) {
            if (!a) return null;
            a = Date.parse(a);
            return isNaN(a) ? null : Math.floor(a / 1E3)
        }

        function G(a) {
            if (!a) return null;
            a = /^P(?:([0-9]*)Y)?(?:([0-9]*)M)?(?:([0-9]*)D)?(?:T(?:([0-9]*)H)?(?:([0-9]*)M)?(?:([0-9.]*)S)?)?$/.exec(a);
            if (!a) return null;
            a = 31536E3 * Number(a[1] || null) + 2592E3 * Number(a[2] || null) + 86400 * Number(a[3] || null) + 3600 * Number(a[4] || null) + 60 * Number(a[5] || null) + Number(a[6] || null);
            return isFinite(a) ? a : null
        }

        function ab(a) {
            var b = /([0-9]+)-([0-9]+)/.exec(a);
            if (!b) return null;
            a = Number(b[1]);
            if (!isFinite(a)) return null;
            b = Number(b[2]);
            return isFinite(b) ? {start: a, end: b} : null
        }

        function bb(a) {
            a = Number(a);
            return a % 1 ? null : a
        }

        function cb(a) {
            a = Number(a);
            return !(a % 1) && 0 < a ? a : null
        }

        function db(a) {
            a = Number(a);
            return !(a % 1) && 0 <= a ? a : null
        };
        var eb = {
            "urn:uuid:1077efec-c0b2-4d02-ace3-3c1e52e2fb4b": "org.w3.clearkey",
            "urn:uuid:edef8ba9-79d6-4ace-a3c8-27dcd51d21ed": "com.widevine.alpha",
            "urn:uuid:9a04f079-9840-4286-ab92-e65be0885f95": "com.microsoft.playready",
            "urn:uuid:f239e769-efa3-4850-9c16-a903c6932efb": "com.adobe.primetime"
        };

        function fb(a, b) {
            var c = gb(a), d = null, e = c.filter(function (a) {
                return "urn:mpeg:dash:mp4protection:2011" == a.ub ? (d = a.init || d, !1) : !0
            }), f = c.map(function (a) {
                return a.keyId
            }).filter(Na), g = null;
            if (0 < f.length && (g = f[0], f.some(Oa(g)))) throw new t(4, 4010);
            f = [];
            0 < e.length ? (f = hb(d, b, e), f.length || (f = [ib("", d)])) : 0 < c.length && (f = C(eb).map(function (a) {
                return ib(a, d)
            }));
            return {eb: g, Wc: d, drmInfos: f, fb: !0}
        }

        function jb(a, b, c) {
            var d = fb(a, b);
            if (c.fb) {
                a = 1 == c.drmInfos.length && !c.drmInfos[0].keySystem;
                b = !d.drmInfos.length;
                if (!c.drmInfos.length || a && !b) c.drmInfos = d.drmInfos;
                c.fb = !1
            } else if (0 < d.drmInfos.length && (c.drmInfos = c.drmInfos.filter(function (a) {
                return d.drmInfos.some(function (b) {
                    return b.keySystem == a.keySystem
                })
            }), !c.drmInfos.length)) throw new t(4, 4008);
            return d.eb || c.eb
        }

        function ib(a, b) {
            return {
                keySystem: a,
                licenseServerUri: "",
                distinctiveIdentifierRequired: !1,
                persistentStateRequired: !1,
                audioRobustness: "",
                videoRobustness: "",
                serverCertificate: null,
                initData: b || []
            }
        }

        function hb(a, b, c) {
            return c.map(function (c) {
                var e = eb[c.ub];
                return e ? [ib(e, c.init || a)] : b(c.node) || []
            }).reduce(A, [])
        }

        function gb(a) {
            return a.map(function (a) {
                var c = a.getAttribute("schemeIdUri"), d = a.getAttribute("cenc:default_KID"),
                    e = D(a, "cenc:pssh").map(Ya);
                if (!c) return null;
                c = c.toLowerCase();
                if (d && (d = d.replace(/-/g, "").toLowerCase(), 0 <= d.indexOf(" "))) throw new t(4, 4009);
                var f = [];
                try {
                    f = e.map(function (a) {
                        return {initDataType: "cenc", initData: Ta(a)}
                    })
                } catch (g) {
                    throw new t(4, 4007);
                }
                return {node: a, ub: c, keyId: d, init: 0 < f.length ? f : null}
            }).filter(Na)
        };
        var kb = /^(?:([^:/?#.]+):)?(?:\/\/(?:([^/?#]*)@)?([^/#?]*?)(?::([0-9]+))?(?=[/#?]|$))?([^?#]+)?(?:\?([^#]*))?(?:#(.*))?$/;

        function lb(a) {
            var b;
            a instanceof lb ? (mb(this, a.R), this.ia = a.ia, this.S = a.S, nb(this, a.ua), this.O = a.O, ob(this, pb(a.a)), this.da = a.da) : a && (b = String(a).match(kb)) ? (mb(this, b[1] || "", !0), this.ia = qb(b[2] || ""), this.S = qb(b[3] || "", !0), nb(this, b[4]), this.O = qb(b[5] || "", !0), ob(this, b[6] || "", !0), this.da = qb(b[7] || "")) : this.a = new rb(null)
        }

        h = lb.prototype;
        h.R = "";
        h.ia = "";
        h.S = "";
        h.ua = null;
        h.O = "";
        h.da = "";
        h.toString = function () {
            var a = [], b = this.R;
            b && a.push(sb(b, tb, !0), ":");
            if (b = this.S) {
                a.push("//");
                var c = this.ia;
                c && a.push(sb(c, tb, !0), "@");
                a.push(encodeURIComponent(b).replace(/%25([0-9a-fA-F]{2})/g, "%$1"));
                b = this.ua;
                null != b && a.push(":", String(b))
            }
            if (b = this.O) this.S && "/" != b.charAt(0) && a.push("/"), a.push(sb(b, "/" == b.charAt(0) ? ub : vb, !0));
            (b = this.a.toString()) && a.push("?", b);
            (b = this.da) && a.push("#", sb(b, wb));
            return a.join("")
        };
        h.resolve = function (a) {
            var b = new lb(this);
            "data" === b.R && (b = new lb);
            var c = !!a.R;
            c ? mb(b, a.R) : c = !!a.ia;
            c ? b.ia = a.ia : c = !!a.S;
            c ? b.S = a.S : c = null != a.ua;
            var d = a.O;
            if (c) nb(b, a.ua); else if (c = !!a.O) {
                if ("/" != d.charAt(0)) if (this.S && !this.O) d = "/" + d; else {
                    var e = b.O.lastIndexOf("/");
                    -1 != e && (d = b.O.substr(0, e + 1) + d)
                }
                if (".." == d || "." == d) d = ""; else if (-1 != d.indexOf("./") || -1 != d.indexOf("/.")) {
                    for (var e = !d.lastIndexOf("/", 0), d = d.split("/"), f = [], g = 0; g < d.length;) {
                        var k = d[g++];
                        "." == k ? e && g == d.length && f.push("") : ".." == k ? ((1 < f.length ||
                            1 == f.length && "" != f[0]) && f.pop(), e && g == d.length && f.push("")) : (f.push(k), e = !0)
                    }
                    d = f.join("/")
                }
            }
            c ? b.O = d : c = "" !== a.a.toString();
            c ? ob(b, pb(a.a)) : c = !!a.da;
            c && (b.da = a.da);
            return b
        };

        function mb(a, b, c) {
            a.R = c ? qb(b, !0) : b;
            a.R && (a.R = a.R.replace(/:$/, ""))
        }

        function nb(a, b) {
            if (b) {
                b = Number(b);
                if (isNaN(b) || 0 > b) throw Error("Bad port number " + b);
                a.ua = b
            } else a.ua = null
        }

        function ob(a, b, c) {
            b instanceof rb ? a.a = b : (c || (b = sb(b, xb)), a.a = new rb(b))
        }

        function qb(a, b) {
            return a ? b ? decodeURI(a) : decodeURIComponent(a) : ""
        }

        function sb(a, b, c) {
            return "string" == typeof a ? (a = encodeURI(a).replace(b, yb), c && (a = a.replace(/%25([0-9a-fA-F]{2})/g, "%$1")), a) : null
        }

        function yb(a) {
            a = a.charCodeAt(0);
            return "%" + (a >> 4 & 15).toString(16) + (a & 15).toString(16)
        }

        var tb = /[#\/\?@]/g, vb = /[\#\?:]/g, ub = /[\#\?]/g, xb = /[\#\?@]/g, wb = /#/g;

        function rb(a) {
            this.b = a || null
        }

        rb.prototype.a = null;
        rb.prototype.c = null;
        rb.prototype.toString = function () {
            if (this.b) return this.b;
            if (!this.a) return "";
            var a = [], b;
            for (b in this.a) for (var c = encodeURIComponent(b), d = this.a[b], e = 0; e < d.length; e++) {
                var f = c;
                "" !== d[e] && (f += "=" + encodeURIComponent(d[e]));
                a.push(f)
            }
            return this.b = a.join("&")
        };

        function pb(a) {
            var b = new rb;
            b.b = a.b;
            if (a.a) {
                var c = {}, d;
                for (d in a.a) c[d] = a.a[d].concat();
                b.a = c;
                b.c = a.c
            }
            return b
        };

        function zb(a, b, c) {
            this.a = a;
            this.K = b;
            this.C = c
        }

        m("shaka.media.InitSegmentReference", zb);

        function H(a, b, c, d, e, f) {
            this.position = a;
            this.startTime = b;
            this.endTime = c;
            this.a = d;
            this.K = e;
            this.C = f
        }

        m("shaka.media.SegmentReference", H);
        var Ab = 1 / 15;

        function Bb(a, b, c, d, e) {
            null !== e && (e = Math.round(e));
            var f = {RepresentationID: b, Number: c, Bandwidth: d, Time: e};
            return a.replace(/\$(RepresentationID|Number|Bandwidth|Time)?(?:%0([0-9]+)d)?\$/g, function (a, b, c) {
                if ("$$" == a) return "$";
                var d = f[b];
                if (null == d) return a;
                "RepresentationID" == b && c && (c = void 0);
                a = d.toString();
                c = window.parseInt(c, 10) || 1;
                return Array(Math.max(0, c - a.length) + 1).join("0") + a
            })
        }

        function Cb(a, b) {
            if (b.length) {
                var c = b[0];
                c.startTime <= Ab && (b[0] = new H(c.position, 0, c.endTime, c.a, c.K, c.C));
                null != a && a != Number.POSITIVE_INFINITY && (c = b[b.length - 1], c.startTime > a || (b[b.length - 1] = new H(c.position, c.startTime, a, c.a, c.K, c.C)))
            }
        }

        function J(a, b) {
            if (!b.length) return a;
            var c = b.map(function (a) {
                return new lb(a)
            });
            return a.map(function (a) {
                return new lb(a)
            }).map(function (a) {
                return c.map(a.resolve.bind(a))
            }).reduce(A, []).map(function (a) {
                return a.toString()
            })
        }

        function Db(a, b) {
            var c = K(a, b, "timescale"), d = 1;
            c && (d = cb(c) || 1);
            c = K(a, b, "duration");
            (c = cb(c || "")) && (c /= d);
            var e = K(a, b, "startNumber"), f = K(a, b, "presentationTimeOffset"), g = db(e || "");
            if (null == e || null == g) g = 1;
            var k = Eb(a, b, "SegmentTimeline"), e = null;
            if (k) {
                for (var e = d, l = Number(f), p = a.J.duration || Number.POSITIVE_INFINITY, k = D(k, "S"), r = [],
                         u = 0, N = 0; N < k.length; ++N) {
                    var E = k[N], I = F(E, "t", db), Za = F(E, "d", db), E = F(E, "r", bb);
                    null != I && (I -= l);
                    if (!Za) break;
                    I = null != I ? I : u;
                    E = E || 0;
                    if (0 > E) if (N + 1 < k.length) {
                        E = F(k[N + 1], "t", db);
                        if (null ==
                            E) break; else if (I >= E) break;
                        E = Math.ceil((E - I) / Za) - 1
                    } else {
                        if (p == Number.POSITIVE_INFINITY) break; else if (I / e >= p) break;
                        E = Math.ceil((p * e - I) / Za) - 1
                    }
                    0 < r.length && I != u && (r[r.length - 1].end = I / e);
                    for (var Qc = 0; Qc <= E; ++Qc) u = I + Za, r.push({start: I / e, end: u / e}), I = u
                }
                e = r
            }
            return {Ka: d, D: c, fa: g, presentationTimeOffset: Number(f) / d || 0, A: e}
        }

        function K(a, b, c) {
            return [b(a.s), b(a.P), b(a.I)].filter(Na).map(function (a) {
                return a.getAttribute(c)
            }).reduce(function (a, b) {
                return a || b
            })
        }

        function Eb(a, b, c) {
            return [b(a.s), b(a.P), b(a.I)].filter(Na).map(function (a) {
                return Xa(a, c)
            }).reduce(function (a, b) {
                return a || b
            })
        };

        function Fb(a) {
            this.b = a;
            this.c = 0 == Gb;
            this.a = 0
        }

        var Gb = 1;

        function Hb(a) {
            return a.a < a.b.byteLength
        }

        function Ib(a) {
            try {
                var b = a.b.getUint8(a.a)
            } catch (c) {
                Jb()
            }
            a.a += 1;
            return b
        }

        function Kb(a) {
            try {
                var b = a.b.getUint16(a.a, a.c)
            } catch (c) {
                Jb()
            }
            a.a += 2;
            return b
        }

        function L(a) {
            try {
                var b = a.b.getUint32(a.a, a.c)
            } catch (c) {
                Jb()
            }
            a.a += 4;
            return b
        }

        function Lb(a) {
            var b, c;
            try {
                a.c ? (b = a.b.getUint32(a.a, !0), c = a.b.getUint32(a.a + 4, !0)) : (c = a.b.getUint32(a.a, !1), b = a.b.getUint32(a.a + 4, !1))
            } catch (d) {
                Jb()
            }
            if (2097151 < c) throw new t(3, 3001);
            a.a += 8;
            return c * Math.pow(2, 32) + b
        }

        function Mb(a, b) {
            a.a + b > a.b.byteLength && Jb();
            var c = a.b.buffer.slice(a.a, a.a + b);
            a.a += b;
            return new Uint8Array(c)
        }

        function M(a, b) {
            a.a + b > a.b.byteLength && Jb();
            a.a += b
        }

        function Jb() {
            throw new t(3, 3E3);
        };

        function Nb(a, b) {
            for (; Hb(b);) {
                var c = b.a, d = L(b), e = L(b);
                1 == d ? d = Lb(b) : d || (d = b.b.byteLength - c);
                if (e == a) return d;
                M(b, d - (b.a - c))
            }
            return -1
        }

        function Ob(a, b) {
            for (var c = new Fb(new DataView(a)),
                     d = [[1836019574, 0], [1953653099, 0], [1835297121, 0], [1835626086, 0], [1937007212, 0], [1937011556, 8], [b, 0]],
                     e = -1, f = 0; f < d.length; f++) {
                var g = d[f][1], e = Nb(d[f][0], c);
                if (-1 == e) return -1;
                M(c, g)
            }
            return e
        };

        function Pb(a, b, c, d) {
            var e = [];
            a = new Fb(new DataView(a));
            var f = Nb(1936286840, a);
            if (-1 == f) throw new t(3, 3004);
            var g = Ib(a);
            M(a, 3);
            M(a, 4);
            var k = L(a);
            if (!k) throw new t(3, 3005);
            var l, p;
            g ? (l = Lb(a), p = Lb(a)) : (l = L(a), p = L(a));
            M(a, 2);
            g = Kb(a);
            d = l - d;
            b = b + f + p;
            for (f = 0; f < g; f++) {
                l = L(a);
                p = (l & 2147483648) >>> 31;
                l &= 2147483647;
                var r = L(a);
                M(a, 4);
                if (1 == p) throw new t(3, 3006);
                e.push(new H(e.length, d / k, (d + r) / k, function () {
                    return c
                }, b, b + l - 1));
                d += r;
                b += l
            }
            return e
        };

        function O(a) {
            this.a = a
        }

        m("shaka.media.SegmentIndex", O);
        O.prototype.o = function () {
            this.a = null;
            return Promise.resolve()
        };
        O.prototype.destroy = O.prototype.o;
        O.prototype.find = function (a) {
            for (var b = this.a.length - 1; 0 <= b; --b) {
                var c = this.a[b];
                if (a >= c.startTime && a < c.endTime) return c.position
            }
            return null
        };
        O.prototype.find = O.prototype.find;
        O.prototype.get = function (a) {
            if (!this.a.length) return null;
            a -= this.a[0].position;
            return 0 > a || a >= this.a.length ? null : this.a[a]
        };
        O.prototype.get = O.prototype.get;
        O.prototype.Ra = function (a) {
            for (var b = [], c = 0, d = 0; c < this.a.length && d < a.length;) {
                var e = this.a[c], f = a[d];
                e.startTime < f.startTime ? (b.push(e), c++) : (e.startTime > f.startTime || (.1 < Math.abs(e.endTime - f.endTime) ? b.push(f) : b.push(e), c++), d++)
            }
            for (; c < this.a.length;) b.push(this.a[c++]);
            if (b.length) for (c = b[b.length - 1].position + 1; d < a.length;) f = a[d++], f = new H(c++, f.startTime, f.endTime, f.a, f.K, f.C), b.push(f); else b = a;
            this.a = b
        };
        O.prototype.merge = O.prototype.Ra;
        O.prototype.Na = function (a) {
            for (var b = 0; b < this.a.length && !(this.a[b].endTime > a); ++b) ;
            this.a.splice(0, b)
        };
        O.prototype.evict = O.prototype.Na;

        function Qb(a) {
            this.b = a;
            this.a = new Fb(a);
            Rb || (Rb = [new Uint8Array([255]), new Uint8Array([127, 255]), new Uint8Array([63, 255, 255]), new Uint8Array([31, 255, 255, 255]), new Uint8Array([15, 255, 255, 255, 255]), new Uint8Array([7, 255, 255, 255, 255, 255]), new Uint8Array([3, 255, 255, 255, 255, 255, 255]), new Uint8Array([1, 255, 255, 255, 255, 255, 255, 255])])
        }

        var Rb;

        function P(a) {
            var b;
            b = Sb(a);
            if (7 < b.length) throw new t(3, 3002);
            for (var c = 0, d = 0; d < b.length; d++) c = 256 * c + b[d];
            b = c;
            c = Sb(a);
            a:{
                for (d = 0; d < Rb.length; d++) if (Wa(c, Rb[d])) {
                    d = !0;
                    break a
                }
                d = !1
            }
            if (d) c = a.b.byteLength - a.a.a; else {
                if (8 == c.length && c[1] & 224) throw new t(3, 3001);
                for (var d = c[0] & (1 << 8 - c.length) - 1, e = 1; e < c.length; e++) d = 256 * d + c[e];
                c = d
            }
            c = a.a.a + c <= a.b.byteLength ? c : a.b.byteLength - a.a.a;
            d = new DataView(a.b.buffer, a.b.byteOffset + a.a.a, c);
            M(a.a, c);
            return new Tb(b, d)
        }

        function Sb(a) {
            var b = Ib(a.a), c;
            for (c = 1; 8 >= c && !(b & 1 << 8 - c); c++) ;
            if (8 < c) throw new t(3, 3002);
            var d = new Uint8Array(c);
            d[0] = b;
            for (b = 1; b < c; b++) d[b] = Ib(a.a);
            return d
        }

        function Tb(a, b) {
            this.id = a;
            this.a = b
        }

        function Ub(a) {
            if (8 < a.a.byteLength) throw new t(3, 3002);
            if (8 == a.a.byteLength && a.a.getUint8(0) & 224) throw new t(3, 3001);
            for (var b = 0, c = 0; c < a.a.byteLength; c++) var d = a.a.getUint8(c), b = 256 * b + d;
            return b
        };

        function Vb(a, b, c, d, e, f) {
            function g() {
                return e
            }

            var k = [];
            a = new Qb(a.a);
            for (var l = -1, p = -1; Hb(a.a);) {
                var r = P(a);
                if (187 == r.id) {
                    var u = Wb(r);
                    u && (r = c * (u.Qc - f), u = b + u.pc, 0 <= l && k.push(new H(k.length, l, r, g, p, u - 1)), l = r, p = u)
                }
            }
            0 <= l && k.push(new H(k.length, l, d, g, p, null));
            return k
        }

        function Wb(a) {
            var b = new Qb(a.a);
            a = P(b);
            if (179 != a.id) throw new t(3, 3013);
            a = Ub(a);
            b = P(b);
            if (183 != b.id) throw new t(3, 3012);
            for (var b = new Qb(b.a), c = 0; Hb(b.a);) {
                var d = P(b);
                if (241 == d.id) {
                    c = Ub(d);
                    break
                }
            }
            return {Qc: a, pc: c}
        };

        function Xb(a, b) {
            var c = Eb(a, b, "Initialization");
            if (!c) return null;
            var d = a.s.M, e = c.getAttribute("sourceURL");
            e && (d = J(a.s.M, [e]));
            var e = 0, f = null;
            if (c = F(c, "range", ab)) e = c.start, f = c.end;
            return new zb(function () {
                return d
            }, e, f)
        }

        function Yb(a, b) {
            var c = K(a, Zb, "presentationTimeOffset"), d = Xb(a, Zb), e;
            e = Number(c);
            var f = a.s.mimeType.split("/")[1];
            if ("text" != a.s.contentType && "mp4" != f && "webm" != f) throw new t(4, 4006);
            if ("webm" == f && !d) throw new t(4, 4005);
            var g = Eb(a, Zb, "RepresentationIndex"), k = K(a, Zb, "indexRange"), l = a.s.M, k = ab(k || "");
            if (g) {
                var p = g.getAttribute("sourceURL");
                p && (l = J(a.s.M, [p]));
                k = F(g, "range", ab, k)
            }
            if (!k) throw new t(4, 4002);
            e = $b(a, b, d, l, k.start, k.end, f, e);
            return {
                createSegmentIndex: e.createSegmentIndex,
                findSegmentPosition: e.findSegmentPosition,
                getSegmentReference: e.getSegmentReference,
                initSegmentReference: d,
                presentationTimeOffset: Number(c) || 0
            }
        }

        function $b(a, b, c, d, e, f, g, k) {
            var l = a.presentationTimeline, p = a.J.start, r = a.J.duration, u = b, N = null;
            return {
                createSegmentIndex: function () {
                    var a = [u(d, e, f), "webm" == g ? u(c.a(), c.K, c.C) : null];
                    u = null;
                    return Promise.all(a).then(function (a) {
                        var b, c = a[0];
                        a = a[1] || null;
                        if ("mp4" == g) c = Pb(c, e, d, k); else {
                            a = new Qb(new DataView(a));
                            if (440786851 != P(a).id) throw new t(3, 3008);
                            var f = P(a);
                            if (408125543 != f.id) throw new t(3, 3009);
                            a = f.a.byteOffset;
                            f = new Qb(f.a);
                            for (b = null; Hb(f.a);) {
                                var u = P(f);
                                if (357149030 == u.id) {
                                    b = u;
                                    break
                                }
                            }
                            if (!b) throw new t(3,
                                3010);
                            f = new Qb(b.a);
                            b = 1E6;
                            for (u = null; Hb(f.a);) {
                                var E = P(f);
                                if (2807729 == E.id) b = Ub(E); else if (17545 == E.id) if (u = E, 4 == u.a.byteLength) u = u.a.getFloat32(0); else if (8 == u.a.byteLength) u = u.a.getFloat64(0); else throw new t(3, 3003);
                            }
                            if (null == u) throw new t(3, 3011);
                            f = b / 1E9;
                            b = u * f;
                            c = P(new Qb(new DataView(c)));
                            if (475249515 != c.id) throw new t(3, 3007);
                            c = Vb(c, a, f, b, d, k)
                        }
                        Cb(r, c);
                        l.Ca(p, c);
                        N = new O(c)
                    })
                }, findSegmentPosition: function (a) {
                    return N.find(a)
                }, getSegmentReference: function (a) {
                    return N.get(a)
                }
            }
        }

        function Zb(a) {
            return a.Ea
        };

        function ac(a, b) {
            var c = Xb(a, bc), d;
            d = cc(a);
            var e = Db(a, bc), f = e.fa;
            f || (f = 1);
            var g = 0;
            e.D ? g = e.D * (f - 1) - e.presentationTimeOffset : e.A && 0 < e.A.length && (g = e.A[0].start);
            d = {D: e.D, startTime: g, fa: f, presentationTimeOffset: e.presentationTimeOffset, A: e.A, sa: d};
            if (!d.D && !d.A && 1 < d.sa.length) throw new t(4, 4002);
            if (!d.D && !a.J.duration && !d.A && 1 == d.sa.length) throw new t(4, 4002);
            if (d.A && !d.A.length) throw new t(4, 4002);
            f = e = null;
            a.I.id && a.s.id && (f = a.I.id + "," + a.s.id, e = b[f]);
            g = dc(a.J.duration, d.fa, a.s.M, d);
            Cb(a.J.duration, g);
            e ? (e.Ra(g), e.Na(a.presentationTimeline.qa())) : (a.presentationTimeline.Ca(a.J.start, g), e = new O(g), f && (b[f] = e));
            return {
                createSegmentIndex: Promise.resolve.bind(Promise),
                findSegmentPosition: e.find.bind(e),
                getSegmentReference: e.get.bind(e),
                initSegmentReference: c,
                presentationTimeOffset: d.presentationTimeOffset
            }
        }

        function bc(a) {
            return a.Z
        }

        function dc(a, b, c, d) {
            var e = d.sa.length;
            d.A && d.A.length != d.sa.length && (e = Math.min(d.A.length, d.sa.length));
            for (var f = [], g = d.startTime, k = 0; k < e; k++) {
                var l = d.sa[k], p = J(c, [l.Rb]), r;
                r = null != d.D ? g + d.D : d.A ? d.A[k].end : g + a;
                f.push(new H(k + b, g, r, function (a) {
                    return a
                }.bind(null, p), l.start, l.end));
                g = r
            }
            return f
        }

        function cc(a) {
            return [a.s.Z, a.P.Z, a.I.Z].filter(Na).map(function (a) {
                return D(a, "SegmentURL")
            }).reduce(function (a, c) {
                return 0 < a.length ? a : c
            }).map(function (a) {
                var c = a.getAttribute("media");
                a = F(a, "mediaRange", ab, {start: 0, end: null});
                return {Rb: c, start: a.start, end: a.end}
            })
        };

        function ec(a, b, c, d) {
            var e = fc(a), f;
            f = Db(a, gc);
            var g = K(a, gc, "media"), k = K(a, gc, "index");
            f = {D: f.D, Ka: f.Ka, fa: f.fa, presentationTimeOffset: f.presentationTimeOffset, A: f.A, Qa: g, Ba: k};
            g = 0 + (f.Ba ? 1 : 0);
            g += f.A ? 1 : 0;
            g += f.D ? 1 : 0;
            if (!g) throw new t(4, 4002);
            1 != g && (f.Ba && (f.A = null), f.D = null);
            if (!f.Ba && !f.Qa) throw new t(4, 4002);
            if (f.Ba) {
                c = a.s.mimeType.split("/")[1];
                if ("mp4" != c && "webm" != c) throw new t(4, 4006);
                if ("webm" == c && !e) throw new t(4, 4005);
                d = Bb(f.Ba, a.s.id, null, a.bandwidth || null, null);
                d = J(a.s.M, [d]);
                a = $b(a, b, e, d,
                    0, null, c, f.presentationTimeOffset)
            } else f.D ? (d || a.presentationTimeline.Sa(f.D), a = hc(a, f)) : (d = b = null, a.I.id && a.s.id && (d = a.I.id + "," + a.s.id, b = c[d]), g = ic(a, f), Cb(a.J.duration, g), b ? (b.Ra(g), b.Na(a.presentationTimeline.qa())) : (a.presentationTimeline.Ca(a.J.start, g), b = new O(g), d && (c[d] = b)), a = {
                createSegmentIndex: Promise.resolve.bind(Promise),
                findSegmentPosition: b.find.bind(b),
                getSegmentReference: b.get.bind(b)
            });
            return {
                createSegmentIndex: a.createSegmentIndex,
                findSegmentPosition: a.findSegmentPosition,
                getSegmentReference: a.getSegmentReference,
                initSegmentReference: e,
                presentationTimeOffset: f.presentationTimeOffset
            }
        }

        function gc(a) {
            return a.Fa
        }

        function hc(a, b) {
            var c = a.J.duration, d = b.D, e = b.fa, f = b.Ka, g = b.Qa, k = a.bandwidth || null, l = a.s.id, p = a.s.M;
            return {
                createSegmentIndex: Promise.resolve.bind(Promise), findSegmentPosition: function (a) {
                    return 0 > a || c && a >= c ? null : Math.floor(a / d)
                }, getSegmentReference: function (a) {
                    var b = a * d;
                    return new H(a, b, b + d, function () {
                        var c = Bb(g, l, a + e, k, b * f);
                        return J(p, [c])
                    }, 0, null)
                }
            }
        }

        function ic(a, b) {
            for (var c = [], d = 0; d < b.A.length; d++) {
                var e = b.A[d].start, f = d + b.fa;
                c.push(new H(f, e, b.A[d].end, function (a, b, c, d, e, f) {
                    a = Bb(a, b, e, c, f);
                    return J(d, [a]).map(function (a) {
                        return a.toString()
                    })
                }.bind(null, b.Qa, a.s.id, a.bandwidth || null, a.s.M, f, (e + b.presentationTimeOffset) * b.Ka), 0, null))
            }
            return c
        }

        function fc(a) {
            var b = K(a, gc, "initialization");
            if (!b) return null;
            var c = a.s.id, d = a.bandwidth || null, e = a.s.M;
            return new zb(function () {
                var a = Bb(b, c, null, d, null);
                return J(e, [a])
            }, 0, null)
        };

        function Q(a) {
            this.f = !1;
            this.a = [];
            this.b = [];
            this.c = [];
            this.h = a || null
        }

        m("shaka.net.NetworkingEngine.RequestType", {MANIFEST: 0, SEGMENT: 1, LICENSE: 2});
        var jc = {};
        m("shaka.net.NetworkingEngine.registerScheme", function (a, b) {
            jc[a] = b
        });
        m("shaka.net.NetworkingEngine.unregisterScheme", function (a) {
            delete jc[a]
        });
        Q.prototype.nc = function (a) {
            this.b.push(a)
        };
        Q.prototype.registerRequestFilter = Q.prototype.nc;
        Q.prototype.Oc = function (a) {
            var b = this.b;
            a = b.indexOf(a);
            0 <= a && b.splice(a, 1)
        };
        Q.prototype.unregisterRequestFilter = Q.prototype.Oc;
        Q.prototype.Eb = function () {
            this.b = []
        };
        Q.prototype.clearAllRequestFilters = Q.prototype.Eb;
        Q.prototype.oc = function (a) {
            this.c.push(a)
        };
        Q.prototype.registerResponseFilter = Q.prototype.oc;
        Q.prototype.Pc = function (a) {
            var b = this.c;
            a = b.indexOf(a);
            0 <= a && b.splice(a, 1)
        };
        Q.prototype.unregisterResponseFilter = Q.prototype.Pc;
        Q.prototype.Fb = function () {
            this.c = []
        };
        Q.prototype.clearAllResponseFilters = Q.prototype.Fb;

        function kc() {
            return {maxAttempts: 2, baseDelay: 1E3, backoffFactor: 2, fuzzFactor: .5, timeout: 0}
        }

        function lc(a, b) {
            return {uris: a, method: "GET", body: null, headers: {}, allowCrossSiteCredentials: !1, retryParameters: b}
        }

        Q.prototype.o = function () {
            this.f = !0;
            this.b = [];
            this.c = [];
            for (var a = [], b = 0; b < this.a.length; ++b) a.push(this.a[b]["catch"](B));
            return Promise.all(a)
        };
        Q.prototype.request = function (a, b) {
            if (this.f) return Promise.reject();
            for (var c = Date.now(), d = this.b, e = 0; e < d.length; e++) try {
                d[e](a, b)
            } catch (l) {
                return Promise.reject(l)
            }
            for (var e = b.retryParameters || {}, d = e.maxAttempts || 1, f = e.backoffFactor || 2,
                     g = null == e.baseDelay ? 1E3 : e.baseDelay, k = this.g(a, b, 0),
                     e = 1; e < d; e++) k = k["catch"](this.i.bind(this, a, b, g, e % b.uris.length)), g *= f;
            this.a.push(k);
            return k.then(function (b) {
                this.a.splice(this.a.indexOf(k), 1);
                var d = Date.now();
                this.h && 1 == a && this.h(c, d, b.data.byteLength);
                return b
            }.bind(this))["catch"](function (a) {
                this.a.splice(this.a.indexOf(k),
                    1);
                return Promise.reject(a)
            }.bind(this))
        };
        Q.prototype.request = Q.prototype.request;
        Q.prototype.g = function (a, b, c) {
            if (this.f) return Promise.reject();
            var d = new lb(b.uris[c]), e = d.R;
            e || (e = location.protocol, e = e.slice(0, -1), mb(d, e), b.uris[c] = d.toString());
            return (e = jc[e]) ? e(b.uris[c], b).then(function (b) {
                for (var c = this.c, d = 0; d < c.length; d++) c[d](a, b);
                return b
            }.bind(this)) : Promise.reject(new t(1, 1E3, d))
        };
        Q.prototype.i = function (a, b, c, d) {
            var e = new v, f = b.retryParameters || {};
            window.setTimeout(e.resolve, c * (1 + (2 * Math.random() - 1) * (null == f.fuzzFactor ? .5 : f.fuzzFactor)));
            return e.then(this.g.bind(this, a, b, d))
        };
        var mc = {}, nc = {};
        m("shaka.media.ManifestParser.registerParserByExtension", function (a, b) {
            nc[a] = b
        });
        m("shaka.media.ManifestParser.registerParserByMime", function (a, b) {
            mc[a] = b
        });

        function oc() {
            var a = {}, b;
            for (b in mc) a[b] = !0;
            for (b in nc) a[b] = !0;
            ["application/dash+xml", "application/x-mpegurl", "application/vnd.apple.mpegurl", "application/vnd.ms-sstr+xml"].forEach(function (b) {
                a[b] = !!mc[b]
            });
            ["mpd", "m3u8", "ism"].forEach(function (b) {
                a[b] = !!nc[b]
            });
            return a
        }

        function pc(a, b, c, d) {
            var e = d;
            e || (d = (new lb(a)).O.split("/").pop().split("."), 1 < d.length && (d = d.pop().toLowerCase(), e = nc[d]));
            if (e) return Promise.resolve(e);
            c = lc([a], c);
            c.method = "HEAD";
            return b.request(0, c).then(function (b) {
                (b = b.headers["content-type"]) && (b = b.toLowerCase());
                return (e = mc[b]) ? e : Promise.reject(new t(4, 4E3, a))
            }, function () {
                return Promise.reject(new t(4, 4E3, a))
            })
        };

        function R(a, b) {
            this.i = a;
            this.h = b;
            this.a = this.c = Number.POSITIVE_INFINITY;
            this.b = 1;
            this.g = this.f = 0
        }

        m("shaka.media.PresentationTimeline", R);
        R.prototype.X = function () {
            return this.c
        };
        R.prototype.getDuration = R.prototype.X;
        R.prototype.xa = function (a) {
            this.c = a
        };
        R.prototype.setDuration = R.prototype.xa;
        R.prototype.wb = function (a) {
            this.g = a
        };
        R.prototype.setClockOffset = R.prototype.wb;
        R.prototype.Kb = function () {
            return this.a
        };
        R.prototype.getSegmentAvailabilityDuration = R.prototype.Kb;
        R.prototype.yb = function (a) {
            this.a = a
        };
        R.prototype.setSegmentAvailabilityDuration = R.prototype.yb;
        R.prototype.Ca = function (a, b) {
            b.length && (this.b = b.reduce(function (a, b) {
                return Math.max(a, b.endTime - b.startTime)
            }, this.b), a || (this.f = Math.max(this.f, b[0].startTime)))
        };
        R.prototype.notifySegments = R.prototype.Ca;
        R.prototype.Sa = function (a) {
            this.b = Math.max(this.b, a)
        };
        R.prototype.notifyMaxSegmentDuration = R.prototype.Sa;
        R.prototype.T = function () {
            return this.c == Number.POSITIVE_INFINITY || this.a < Number.POSITIVE_INFINITY
        };
        R.prototype.isLive = R.prototype.T;
        R.prototype.pa = function () {
            return Math.max(Math.min(this.f, this.Y()), this.qa())
        };
        R.prototype.getEarliestStart = R.prototype.pa;
        R.prototype.qa = function () {
            return this.a == Number.POSITIVE_INFINITY ? 0 : Math.max(0, this.Y() - this.a - this.h)
        };
        R.prototype.getSegmentAvailabilityStart = R.prototype.qa;
        R.prototype.Y = function () {
            return null != this.i && this.T() ? Math.min(Math.max(0, (Date.now() + this.g) / 1E3 - this.b - this.i), this.c) : this.c
        };
        R.prototype.getSegmentAvailabilityEnd = R.prototype.Y;
        R.prototype.hb = function () {
            return Math.max(0, this.Y() - (this.T() ? this.h : 0))
        };
        R.prototype.getSeekRangeEnd = R.prototype.hb;

        function qc(a, b) {
            this.g = S[b];
            this.c = a;
            this.h = 0;
            this.f = Number.POSITIVE_INFINITY;
            this.a = this.b = null
        }

        var S = {};
        m("shaka.media.TextEngine.registerParser", function (a, b) {
            S[a] = b
        });
        m("shaka.media.TextEngine.unregisterParser", function (a) {
            delete S[a]
        });
        qc.prototype.o = function () {
            this.c && rc(this, function () {
                return !0
            });
            this.c = this.g = null;
            return Promise.resolve()
        };

        function sc(a, b, c, d) {
            var e = a.h;
            return Promise.resolve().then(function () {
                var a = this.g(b, c, d);
                if (null != c && null != d) {
                    for (var g = 0; g < a.length; ++g) {
                        a[g].startTime += e;
                        a[g].endTime += e;
                        if (a[g].startTime >= this.f) break;
                        this.c.addCue(a[g])
                    }
                    null == this.b && (this.b = c);
                    this.a = Math.min(d, this.f)
                }
            }.bind(a))
        }

        qc.prototype.remove = function (a, b) {
            return Promise.resolve().then(function () {
                rc(this, function (c) {
                    return c.startTime >= b || c.endTime <= a ? !1 : !0
                });
                null == this.b || b <= this.b || a >= this.a || (a <= this.b && b >= this.a ? this.b = this.a = null : a <= this.b && b < this.a ? this.b = b : a > this.b && b >= this.a && (this.a = a))
            }.bind(this))
        };

        function tc(a, b) {
            return null == a.a || a.a < b || b < a.b ? 0 : a.a - b
        }

        function rc(a, b) {
            for (var c = a.c.cues, d = [], e = 0; e < c.length; ++e) b(c[e]) && d.push(c[e]);
            for (e = 0; e < d.length; ++e) a.c.removeCue(d[e])
        };

        function uc(a, b, c) {
            return c == b || a >= vc && c == b.split("-")[0] || a >= wc && c.split("-")[0] == b.split("-")[0] ? !0 : !1
        }

        var vc = 1, wc = 2;

        function xc(a) {
            a = a.toLowerCase().split("-");
            var b = yc[a[0]];
            b && (a[0] = b);
            return a.join("-")
        }

        var yc = {
            aar: "aa",
            abk: "ab",
            afr: "af",
            aka: "ak",
            alb: "sq",
            amh: "am",
            ara: "ar",
            arg: "an",
            arm: "hy",
            asm: "as",
            ava: "av",
            ave: "ae",
            aym: "ay",
            aze: "az",
            bak: "ba",
            bam: "bm",
            baq: "eu",
            bel: "be",
            ben: "bn",
            bih: "bh",
            bis: "bi",
            bod: "bo",
            bos: "bs",
            bre: "br",
            bul: "bg",
            bur: "my",
            cat: "ca",
            ces: "cs",
            cha: "ch",
            che: "ce",
            chi: "zh",
            chu: "cu",
            chv: "cv",
            cor: "kw",
            cos: "co",
            cre: "cr",
            cym: "cy",
            cze: "cs",
            dan: "da",
            deu: "de",
            div: "dv",
            dut: "nl",
            dzo: "dz",
            ell: "el",
            eng: "en",
            epo: "eo",
            est: "et",
            eus: "eu",
            ewe: "ee",
            fao: "fo",
            fas: "fa",
            fij: "fj",
            fin: "fi",
            fra: "fr",
            fre: "fr",
            fry: "fy",
            ful: "ff",
            geo: "ka",
            ger: "de",
            gla: "gd",
            gle: "ga",
            glg: "gl",
            glv: "gv",
            gre: "el",
            grn: "gn",
            guj: "gu",
            hat: "ht",
            hau: "ha",
            heb: "he",
            her: "hz",
            hin: "hi",
            hmo: "ho",
            hrv: "hr",
            hun: "hu",
            hye: "hy",
            ibo: "ig",
            ice: "is",
            ido: "io",
            iii: "ii",
            iku: "iu",
            ile: "ie",
            ina: "ia",
            ind: "id",
            ipk: "ik",
            isl: "is",
            ita: "it",
            jav: "jv",
            jpn: "ja",
            kal: "kl",
            kan: "kn",
            kas: "ks",
            kat: "ka",
            kau: "kr",
            kaz: "kk",
            khm: "km",
            kik: "ki",
            kin: "rw",
            kir: "ky",
            kom: "kv",
            kon: "kg",
            kor: "ko",
            kua: "kj",
            kur: "ku",
            lao: "lo",
            lat: "la",
            lav: "lv",
            lim: "li",
            lin: "ln",
            lit: "lt",
            ltz: "lb",
            lub: "lu",
            lug: "lg",
            mac: "mk",
            mah: "mh",
            mal: "ml",
            mao: "mi",
            mar: "mr",
            may: "ms",
            mkd: "mk",
            mlg: "mg",
            mlt: "mt",
            mon: "mn",
            mri: "mi",
            msa: "ms",
            mya: "my",
            nau: "na",
            nav: "nv",
            nbl: "nr",
            nde: "nd",
            ndo: "ng",
            nep: "ne",
            nld: "nl",
            nno: "nn",
            nob: "nb",
            nor: "no",
            nya: "ny",
            oci: "oc",
            oji: "oj",
            ori: "or",
            orm: "om",
            oss: "os",
            pan: "pa",
            per: "fa",
            pli: "pi",
            pol: "pl",
            por: "pt",
            pus: "ps",
            que: "qu",
            roh: "rm",
            ron: "ro",
            rum: "ro",
            run: "rn",
            rus: "ru",
            sag: "sg",
            san: "sa",
            sin: "si",
            slk: "sk",
            slo: "sk",
            slv: "sl",
            sme: "se",
            smo: "sm",
            sna: "sn",
            snd: "sd",
            som: "so",
            sot: "st",
            spa: "es",
            sqi: "sq",
            srd: "sc",
            srp: "sr",
            ssw: "ss",
            sun: "su",
            swa: "sw",
            swe: "sv",
            tah: "ty",
            tam: "ta",
            tat: "tt",
            tel: "te",
            tgk: "tg",
            tgl: "tl",
            tha: "th",
            tib: "bo",
            tir: "ti",
            ton: "to",
            tsn: "tn",
            tso: "ts",
            tuk: "tk",
            tur: "tr",
            twi: "tw",
            uig: "ug",
            ukr: "uk",
            urd: "ur",
            uzb: "uz",
            ven: "ve",
            vie: "vi",
            vol: "vo",
            wel: "cy",
            wln: "wa",
            wol: "wo",
            xho: "xh",
            yid: "yi",
            yor: "yo",
            zha: "za",
            zho: "zh",
            zul: "zu"
        };

        function zc(a) {
            if (!a) return "";
            a = Ac(new Uint8Array(a));
            a = escape(a);
            try {
                return decodeURIComponent(a)
            } catch (b) {
                throw new t(2, 2004);
            }
        }

        function Bc(a, b) {
            if (!a) return "";
            if (a.byteLength % 2) throw new t(2, 2004);
            var c;
            if (a instanceof ArrayBuffer) c = a; else {
                var d = new Uint8Array(a.byteLength);
                d.set(new Uint8Array(a));
                c = d.buffer
            }
            var d = a.byteLength / 2, e = new Uint16Array(d);
            c = new DataView(c);
            for (var f = 0; f < d; f++) e[f] = c.getUint16(2 * f, b);
            return Ac(e)
        }

        function Cc(a) {
            var b = new Uint8Array(a);
            if (239 == b[0] && 187 == b[1] && 191 == b[2]) return zc(b.subarray(3));
            if (254 == b[0] && 255 == b[1]) return Bc(b.subarray(2), !1);
            if (255 == b[0] && 254 == b[1]) return Bc(b.subarray(2), !0);
            var c = function (a, b) {
                return a.byteLength <= b || 32 <= a[b] && 126 >= a[b]
            }.bind(null, b);
            if (b[0] || b[2]) {
                if (!b[1] && !b[3]) return Bc(a, !0);
                if (c(0) && c(1) && c(2) && c(3)) return zc(a)
            } else return Bc(a, !1);
            throw new t(2, 2003);
        }

        function Dc(a) {
            a = unescape(encodeURIComponent(a));
            for (var b = new Uint8Array(a.length), c = 0; c < a.length; ++c) b[c] = a.charCodeAt(c);
            return b.buffer
        }

        function Ac(a) {
            for (var b = "",
                     c = 0; c < a.length; c += 16E3) b += String.fromCharCode.apply(null, a.subarray(c, c + 16E3));
            return b
        };

        function Ec() {
            this.l = this.j = this.c = this.a = null;
            this.g = [];
            this.b = null;
            this.h = [];
            this.u = 1;
            this.i = {};
            this.m = 0;
            this.f = null
        }

        h = Ec.prototype;
        h.configure = function (a) {
            this.c = a
        };
        h.start = function (a, b, c, d) {
            this.g = [a];
            this.a = b;
            this.j = c;
            this.l = d;
            return Fc(this).then(function () {
                this.a && Gc(this, 0);
                return this.b
            }.bind(this))
        };
        h.stop = function () {
            this.c = this.l = this.j = this.a = null;
            this.g = [];
            this.b = null;
            this.h = [];
            this.i = {};
            null != this.f && (window.clearTimeout(this.f), this.f = null);
            return Promise.resolve()
        };

        function Fc(a) {
            return a.a.request(0, lc(a.g, a.c.retryParameters)).then(function (a) {
                if (this.a) return Hc(this, a.data, a.uri)
            }.bind(a))
        }

        function Hc(a, b, c) {
            var d = Cc(b), e = new DOMParser, f = null;
            b = null;
            try {
                f = e.parseFromString(d, "text/xml")
            } catch (r) {
            }
            f && "MPD" == f.documentElement.tagName && (b = f.documentElement);
            if (!b) throw new t(4, 4001);
            c = [c];
            d = D(b, "Location").map(Ya).filter(Na);
            0 < d.length && (c = a.g = d);
            d = D(b, "BaseURL").map(Ya);
            c = J(c, d);
            var g = F(b, "minBufferTime", G);
            a.m = F(b, "minimumUpdatePeriod", G, -1);
            var f = F(b, "availabilityStartTime", $a), d = F(b, "timeShiftBufferDepth", G),
                k = F(b, "suggestedPresentationDelay", G), e = F(b, "maxSegmentDuration", G), l;
            l = a.b ?
                a.b.presentationTimeline : new R(f, null != k ? k : 5);
            var f = Ic(a, {presentationTimeline: l, I: null, J: null, P: null, s: null, bandwidth: void 0}, c, b),
                p = f.periods;
            l.xa(f.duration || Number.POSITIVE_INFINITY);
            l.yb(null != d ? d : Number.POSITIVE_INFINITY);
            l.Sa(e || 1);
            if (a.b) return Promise.resolve();
            b = D(b, "UTCTiming");
            return Jc(a, c, b, l.T()).then(function (a) {
                this.a && (l.wb(a), this.b = {
                    presentationTimeline: l,
                    periods: p,
                    offlineSessionIds: [],
                    minBufferTime: g || 0
                })
            }.bind(a))
        }

        function Ic(a, b, c, d) {
            var e = F(d, "mediaPresentationDuration", G), f = [], g = 0;
            d = D(d, "Period");
            for (var k = 0; k < d.length; k++) {
                var l = d[k], g = F(l, "start", G, g), p = F(l, "duration", G);
                if (null == p) if (k + 1 != d.length) {
                    var r = F(d[k + 1], "start", G);
                    null != r && (p = r - g)
                } else null != e && (p = e - g);
                var r = a, u = b, l = {start: g, duration: p, node: l};
                u.I = Kc(l.node, null, c);
                u.J = l;
                u.I.id || (u.I.id = "__shaka_period_" + l.start);
                r = D(l.node, "AdaptationSet").map(r.ic.bind(r, u));
                if (!r.length) throw new t(4, 4004);
                r = Lc(r);
                l = {startTime: l.start, streamSets: r};
                f.push(l);
                r = b.I.id;
                a.h.every(Oa(r)) && (a.j(l), a.h.push(r), a.b && a.b.periods.push(l));
                if (null == p) {
                    g = null;
                    break
                }
                g += p
            }
            return null != e ? {periods: f, duration: e} : {periods: f, duration: g}
        }

        h.ic = function (a, b) {
            a.P = Kc(b, a.I, null);
            var c = !1, d = Xa(b, "Role"), e = void 0;
            "text" == a.P.contentType && (e = "subtitle");
            if (d) {
                var f = d.getAttribute("schemeIdUri");
                if (null == f || "urn:mpeg:dash:role:2011" == f) switch (d = d.getAttribute("value"), d) {
                    case "main":
                        c = !0;
                        break;
                    case "caption":
                    case "subtitle":
                        e = d
                }
            }
            var g = [];
            D(b, "SupplementalProperty").forEach(function (a) {
                "http://dashif.org/descriptor/AdaptationSetSwitching" == a.getAttribute("schemeIdURI") && (a = a.getAttribute("value")) && g.push.apply(g, a.split(","))
            });
            d = D(b, "ContentProtection");
            d = fb(d, this.c.dash.customScheme);
            f = xc(b.getAttribute("lang") || "und");
            e = D(b, "Representation").map(this.jc.bind(this, a, d, e, f)).filter(function (a) {
                return !!a
            });
            if (!e.length) throw new t(4, 4003);
            if (!a.P.contentType) {
                var k = e[0].mimeType, l = e[0].codecs, p = k;
                l && (p += '; codecs="' + l + '"');
                a.P.contentType = S[p] ? "text" : k.split("/")[0]
            }
            return {
                id: a.P.id || "__fake__" + this.u++,
                contentType: a.P.contentType,
                language: f,
                Qb: c,
                streams: e,
                drmInfos: d.drmInfos,
                Mc: g
            }
        };
        h.jc = function (a, b, c, d, e) {
            a.s = Kc(e, a.P, null);
            if (!Mc(a.s)) return null;
            a.bandwidth = F(e, "bandwidth", cb) || void 0;
            var f;
            f = this.rc.bind(this);
            if (a.s.Ea) f = Yb(a, f); else if (a.s.Z) f = ac(a, this.i); else if (a.s.Fa) f = ec(a, f, this.i, !!this.b); else {
                var g = a.s.M, k = a.J.duration || 0;
                f = {
                    createSegmentIndex: Promise.resolve.bind(Promise), findSegmentPosition: function (a) {
                        return 0 <= a && a < k ? 1 : null
                    }, getSegmentReference: function (a) {
                        return 1 != a ? null : new H(1, 0, k, function () {
                            return g
                        }, 0, null)
                    }, initSegmentReference: null, presentationTimeOffset: 0
                }
            }
            e =
                D(e, "ContentProtection");
            e = jb(e, this.c.dash.customScheme, b);
            return {
                id: this.u++,
                createSegmentIndex: f.createSegmentIndex,
                findSegmentPosition: f.findSegmentPosition,
                getSegmentReference: f.getSegmentReference,
                initSegmentReference: f.initSegmentReference,
                presentationTimeOffset: f.presentationTimeOffset,
                mimeType: a.s.mimeType,
                codecs: a.s.codecs,
                bandwidth: a.bandwidth,
                width: a.s.width,
                height: a.s.height,
                kind: c,
                encrypted: 0 < b.drmInfos.length,
                keyId: e,
                language: d,
                allowedByApplication: !0,
                allowedByKeySystem: !0
            }
        };
        h.Hc = function () {
            this.f = null;
            var a = Date.now();
            Fc(this).then(function () {
                this.a && Gc(this, (Date.now() - a) / 1E3)
            }.bind(this))["catch"](function (a) {
                this.l(a);
                this.a && Gc(this, 0)
            }.bind(this))
        };

        function Gc(a, b) {
            0 > a.m || (a.f = window.setTimeout(a.Hc.bind(a), 1E3 * Math.max(Math.max(3, a.m) - b, 0)))
        }

        function Kc(a, b, c) {
            b = b || {contentType: "", mimeType: "", codecs: ""};
            c = c || b.M;
            var d = D(a, "BaseURL").map(Ya), e = a.getAttribute("contentType") || b.contentType,
                f = a.getAttribute("mimeType") || b.mimeType;
            e || (e = f.split("/")[0]);
            return {
                M: J(c, d),
                Ea: Xa(a, "SegmentBase") || b.Ea,
                Z: Xa(a, "SegmentList") || b.Z,
                Fa: Xa(a, "SegmentTemplate") || b.Fa,
                width: F(a, "width", db) || b.width,
                height: F(a, "height", db) || b.height,
                contentType: e,
                mimeType: f,
                codecs: a.getAttribute("codecs") || b.codecs,
                id: a.getAttribute("id")
            }
        }

        function Lc(a) {
            var b = {};
            a.forEach(function (a) {
                b[a.id] = [a]
            });
            a.forEach(function (a) {
                var c = b[a.id];
                a.Mc.forEach(function (a) {
                    (a = b[a]) && a != c && (c.push.apply(c, a), a.forEach(function (a) {
                        b[a.id] = c
                    }))
                })
            });
            var c = [], d = [];
            C(b).forEach(function (a) {
                if (!(0 <= d.indexOf(a))) {
                    d.push(a);
                    var b = new Ea;
                    a.forEach(function (a) {
                        b.push(a.contentType || "", a)
                    });
                    b.keys().forEach(function (a) {
                        var d = new Ea;
                        b.get(a).forEach(function (a) {
                            d.push(a.language, a)
                        });
                        d.keys().forEach(function (b) {
                            var e = d.get(b);
                            b = {
                                language: b, type: a, primary: e.some(function (a) {
                                    return a.Qb
                                }),
                                drmInfos: e.map(function (a) {
                                    return a.drmInfos
                                }).reduce(A, []), streams: e.map(function (a) {
                                    return a.streams
                                }).reduce(A, [])
                            };
                            c.push(b)
                        })
                    })
                }
            });
            return c
        }

        function Mc(a) {
            var b;
            b = 0 + (a.Ea ? 1 : 0);
            b += a.Z ? 1 : 0;
            b += a.Fa ? 1 : 0;
            if (!b) return "text" == a.contentType || "application" == a.contentType ? !0 : !1;
            1 != b && (a.Ea && (a.Z = null), a.Fa = null);
            return !0
        }

        function Nc(a, b, c, d) {
            b = J(b, [c]);
            b = lc(b, a.c.retryParameters);
            b.method = d;
            return a.a.request(0, b).then(function (a) {
                if ("HEAD" == d) {
                    if (!a.headers || !a.headers.date) return 0;
                    a = a.headers.date
                } else a = Cc(a.data);
                a = Date.parse(a);
                return isNaN(a) ? 0 : a - Date.now()
            })
        }

        function Jc(a, b, c, d) {
            c = c.map(function (a) {
                return {scheme: a.getAttribute("schemeIdUri"), value: a.getAttribute("value")}
            });
            var e = a.c.dash.clockSyncUri;
            d && !c.length && e && c.push({scheme: "urn:mpeg:dash:utc:http-head:2014", value: e});
            return Ma(c, function (a) {
                var c = a.value;
                switch (a.scheme) {
                    case "urn:mpeg:dash:utc:http-head:2014":
                    case "urn:mpeg:dash:utc:http-head:2012":
                        return Nc(this, b, c, "HEAD");
                    case "urn:mpeg:dash:utc:http-xsdate:2014":
                    case "urn:mpeg:dash:utc:http-iso:2014":
                    case "urn:mpeg:dash:utc:http-xsdate:2012":
                    case "urn:mpeg:dash:utc:http-iso:2012":
                        return Nc(this,
                            b, c, "GET");
                    case "urn:mpeg:dash:utc:direct:2014":
                    case "urn:mpeg:dash:utc:direct:2012":
                        return a = Date.parse(c), isNaN(a) ? 0 : a - Date.now();
                    case "urn:mpeg:dash:utc:http-ntp:2014":
                    case "urn:mpeg:dash:utc:ntp:2014":
                    case "urn:mpeg:dash:utc:sntp:2014":
                        return Promise.reject();
                    default:
                        return Promise.reject()
                }
            }.bind(a))["catch"](function () {
                return 0
            })
        }

        h.rc = function (a, b, c) {
            a = lc(a, this.c.retryParameters);
            null != b && (a.headers.Range = "bytes=" + b + "-" + (null != c ? c : ""));
            return this.a.request(1, a).then(function (a) {
                return a.data
            })
        };
        nc.mpd = Ec;
        mc["application/dash+xml"] = Ec;

        function Oc(a, b, c) {
            for (var d = 0; d < a.length; ++d) if (c(a[d], b)) return d;
            return -1
        };

        function Pc(a) {
            this.a = null;
            this.b = function () {
                this.a = null;
                a()
            }.bind(this)
        }

        function Rc(a) {
            null != a.a && (clearTimeout(a.a), a.a = null)
        }

        function Sc(a) {
            Rc(a);
            a.a = setTimeout(a.b, 100)
        };

        function Tc(a, b, c) {
            this.l = this.h = this.m = null;
            this.B = !1;
            this.b = null;
            this.f = new w;
            this.a = [];
            this.u = [];
            this.j = new v;
            this.G = a;
            this.i = null;
            this.g = function (a) {
                this.j.reject(a);
                b(a)
            }.bind(this);
            this.w = {};
            this.L = c;
            this.v = new Pc(this.mc.bind(this));
            this.F = this.c = !1;
            this.j["catch"](function () {
            })
        }

        h = Tc.prototype;
        h.o = function () {
            this.c = !0;
            var a = this.a.map(function (a) {
                a.wa.close()["catch"](B);
                return a.wa.closed
            });
            this.j.reject();
            this.f && a.push(this.f.o());
            this.l && a.push(this.l.setMediaKeys(null)["catch"](B));
            this.v && Rc(this.v);
            this.f = this.l = this.h = this.m = this.b = this.v = null;
            this.a = [];
            this.u = [];
            this.g = this.i = this.G = null;
            return Promise.all(a)
        };
        h.configure = function (a) {
            this.i = a
        };
        h.init = function (a, b) {
            var c = {}, d = [];
            this.F = b;
            this.u = a.offlineSessionIds;
            Vc(this, a, b || 0 < a.offlineSessionIds.length, c, d);
            return d.length ? Wc(this, c, d) : (this.B = !0, Promise.resolve())
        };

        function Xc(a, b) {
            if (!a.h) return x(a.f, b, "encrypted", function () {
                this.f.ha(b, "encrypted");
                this.g(new t(6, 6010))
            }.bind(a)), Promise.resolve();
            a.l = b;
            var c = a.l.setMediaKeys(a.h), c = c["catch"](function (a) {
                return Promise.reject(new t(6, 6003, a.message))
            }), d = null;
            a.b.serverCertificate && (d = a.h.setServerCertificate(a.b.serverCertificate), d = d["catch"](function (a) {
                return Promise.reject(new t(6, 6004, a.message))
            }));
            return Promise.all([c, d]).then(function () {
                if (this.c) return Promise.reject();
                Yc(this);
                this.b.initData.length ||
                this.u.length || x(this.f, this.l, "encrypted", this.Tb.bind(this))
            }.bind(a))["catch"](function (a) {
                return this.c ? Promise.resolve() : Promise.reject(a)
            }.bind(a))
        }

        function Zc(a, b) {
            return Promise.all(b.map(function (a) {
                return $c(this, a).then(function (a) {
                    if (a) return a.remove()
                })
            }.bind(a)))
        }

        function Yc(a) {
            var b = a.b ? a.b.initData : [];
            b.forEach(function (a) {
                ad(this, a.initDataType, a.initData)
            }.bind(a));
            a.u.forEach(function (a) {
                $c(this, a)
            }.bind(a));
            b.length || a.u.length || a.j.resolve();
            return a.j
        }

        h.keySystem = function () {
            return this.b ? this.b.keySystem : ""
        };

        function bd(a) {
            return a.a.map(function (a) {
                return a.wa.sessionId
            })
        }

        function Vc(a, b, c, d, e) {
            var f = cd(a), g = 0 <= navigator.userAgent.indexOf("Edge/");
            b.periods.forEach(function (a) {
                a.streamSets.forEach(function (a) {
                    "text" != a.type && (f && (a.drmInfos = [f]), a.drmInfos.forEach(function (b) {
                        dd(this, b);
                        var f = d[b.keySystem];
                        f || (f = {
                            audioCapabilities: [],
                            videoCapabilities: [],
                            distinctiveIdentifier: "optional",
                            persistentState: c ? "required" : "optional",
                            sessionTypes: [c ? "persistent-license" : "temporary"],
                            label: b.keySystem,
                            drmInfos: []
                        }, d[b.keySystem] = f, e.push(b.keySystem));
                        f.drmInfos.push(b);
                        b.distinctiveIdentifierRequired &&
                        (f.distinctiveIdentifier = "required");
                        b.persistentStateRequired && (f.persistentState = "required");
                        var k = "video" == a.type ? f.videoCapabilities : f.audioCapabilities,
                            N = ("video" == a.type ? b.videoRobustness : b.audioRobustness) || "";
                        a.streams.forEach(function (a) {
                            var c = a.mimeType;
                            a.codecs && (c += '; codecs="' + a.codecs + '"');
                            g && "com.microsoft.playready" == b.keySystem && k.length || k.push({
                                robustness: N,
                                contentType: c
                            })
                        }.bind(this))
                    }.bind(this)))
                }.bind(this))
            }.bind(a))
        }

        function Wc(a, b, c) {
            if (1 == c.length && "" == c[0]) return Promise.reject(new t(6, 6E3));
            var d = new v, e = d;
            [!0, !1].forEach(function (a) {
                c.forEach(function (c) {
                    var d = b[c];
                    d.drmInfos.some(function (a) {
                        return !!a.licenseServerUri
                    }) == a && (d.audioCapabilities.length || delete d.audioCapabilities, d.videoCapabilities.length || delete d.videoCapabilities, e = e["catch"](function () {
                        return this.c ? Promise.reject() : navigator.requestMediaKeySystemAccess(c, [d])
                    }.bind(this)))
                }.bind(this))
            }.bind(a));
            e = e["catch"](function () {
                return Promise.reject(new t(6,
                    6001))
            });
            e = e.then(function (a) {
                if (this.c) return Promise.reject();
                var c = a.getConfiguration();
                this.m = (c.audioCapabilities || []).concat(c.videoCapabilities || []).map(function (a) {
                    return a.contentType
                });
                this.m.length || (this.m = null);
                c = b[a.keySystem];
                ed(this, a.keySystem, c, c.drmInfos);
                return this.b.licenseServerUri ? a.createMediaKeys() : Promise.reject(new t(6, 6012))
            }.bind(a)).then(function (a) {
                if (this.c) return Promise.reject();
                this.h = a;
                this.B = !0
            }.bind(a))["catch"](function (a) {
                if (this.c) return Promise.resolve();
                this.m = this.b = null;
                return a instanceof t ? Promise.reject(a) : Promise.reject(new t(6, 6002, a.message))
            }.bind(a));
            d.reject();
            return e
        }

        function dd(a, b) {
            var c = b.keySystem;
            if (c) {
                if (!b.licenseServerUri) {
                    var d = a.i.servers[c];
                    d && (b.licenseServerUri = d)
                }
                if (c = a.i.advanced[c]) b.distinctiveIdentifierRequired || (b.distinctiveIdentifierRequired = c.distinctiveIdentifierRequired), b.persistentStateRequired || (b.persistentStateRequired = c.persistentStateRequired), b.videoRobustness || (b.videoRobustness = c.videoRobustness), b.audioRobustness || (b.audioRobustness = c.audioRobustness), b.serverCertificate || (b.serverCertificate = c.serverCertificate)
            }
        }

        function cd(a) {
            if (Pa(a.i.clearKeys)) return null;
            var b = [], c = [], d;
            for (d in a.i.clearKeys) {
                var e = a.i.clearKeys[d], f = Ua(d), e = Ua(e), f = {kty: "oct", kid: Sa(f), k: Sa(e)};
                b.push(f);
                c.push(f.kid)
            }
            a = JSON.stringify({keys: b});
            c = JSON.stringify({kids: c});
            c = [{initData: new Uint8Array(Dc(c)), initDataType: "keyids"}];
            return {
                keySystem: "org.w3.clearkey",
                licenseServerUri: "data:application/json;base64," + window.btoa(a),
                distinctiveIdentifierRequired: !1,
                persistentStateRequired: !1,
                audioRobustness: "",
                videoRobustness: "",
                serverCertificate: null,
                initData: c
            }
        }

        function ed(a, b, c, d) {
            var e = [], f = [], g = [];
            fd(d, e, f, g);
            a.b = {
                keySystem: b,
                licenseServerUri: e[0],
                distinctiveIdentifierRequired: "required" == c.distinctiveIdentifier,
                persistentStateRequired: "required" == c.persistentState,
                audioRobustness: c.audioCapabilities ? c.audioCapabilities[0].robustness : "",
                videoRobustness: c.videoCapabilities ? c.videoCapabilities[0].robustness : "",
                serverCertificate: f[0],
                initData: g
            }
        }

        function fd(a, b, c, d) {
            function e(a, b) {
                return a.initDataType == b.initDataType && Wa(a.initData, b.initData)
            }

            a.forEach(function (a) {
                -1 == b.indexOf(a.licenseServerUri) && b.push(a.licenseServerUri);
                a.serverCertificate && -1 == Oc(c, a.serverCertificate, Wa) && c.push(a.serverCertificate);
                a.initData && a.initData.forEach(function (a) {
                    -1 == Oc(d, a, e) && d.push(a)
                })
            })
        }

        h.Tb = function (a) {
            for (var b = new Uint8Array(a.initData), c = 0; c < this.a.length; ++c) if (Wa(b, this.a[c].initData)) return;
            ad(this, a.initDataType, b)
        };

        function $c(a, b) {
            var c;
            try {
                c = a.h.createSession("persistent-license")
            } catch (f) {
                var d = new t(6, 6005, f.message);
                a.g(d);
                return Promise.reject(d)
            }
            x(a.f, c, "message", a.ob.bind(a));
            x(a.f, c, "keystatuseschange", a.jb.bind(a));
            var e = {initData: null, wa: c, loaded: !1};
            a.a.push(e);
            return c.load(b).then(function (a) {
                if (!this.c) {
                    if (a) return e.loaded = !0, this.a.every(function (a) {
                        return a.loaded
                    }) && this.j.resolve(), c;
                    this.a.splice(this.a.indexOf(e), 1);
                    this.g(new t(6, 6013))
                }
            }.bind(a), function (a) {
                this.c || (this.a.splice(this.a.indexOf(e),
                    1), this.g(new t(6, 6005, a.message)))
            }.bind(a))
        }

        function ad(a, b, c) {
            var d;
            try {
                d = a.F ? a.h.createSession("persistent-license") : a.h.createSession()
            } catch (e) {
                a.g(new t(6, 6005, e.message));
                return
            }
            x(a.f, d, "message", a.ob.bind(a));
            x(a.f, d, "keystatuseschange", a.jb.bind(a));
            a.a.push({initData: c, wa: d, loaded: !1});
            d.generateRequest(b, c.buffer)["catch"](function (a) {
                if (!this.c) {
                    for (var b = 0; b < this.a.length; ++b) if (this.a[b].wa == d) {
                        this.a.splice(b, 1);
                        break
                    }
                    this.g(new t(6, 6006, a.message))
                }
            }.bind(a))
        }

        h.ob = function (a) {
            var b = a.target, c = lc([this.b.licenseServerUri], this.i.retryParameters);
            c.body = a.message;
            c.method = "POST";
            "com.microsoft.playready" == this.b.keySystem && gd(c);
            this.G.request(2, c).then(function (a) {
                return this.c ? Promise.reject() : b.update(a.data)
            }.bind(this), function (a) {
                if (this.c) return Promise.resolve();
                this.g(new t(6, 6007, a))
            }.bind(this))["catch"](function (a) {
                if (this.c) return Promise.resolve();
                this.g(new t(6, 6008, a.message))
            }.bind(this))
        };

        function gd(a) {
            for (var b = Bc(a.body, !0), b = (new DOMParser).parseFromString(b, "application/xml"),
                     c = b.getElementsByTagName("HttpHeader"),
                     d = 0; d < c.length; ++d) a.headers[c[d].querySelector("name").textContent] = c[d].querySelector("value").textContent;
            a.body = Ta(b.querySelector("Challenge").textContent).buffer
        }

        h.jb = function (a) {
            a = a.target;
            var b;
            for (b = 0; b < this.a.length && this.a[b].wa != a; ++b) ;
            if (b != this.a.length) {
                var c = a.keyStatuses, d = !1;
                c.forEach || (c = []);
                c.forEach(function (a, c) {
                    if ("string" == typeof c) {
                        var g = c;
                        c = a;
                        a = g
                    }
                    if ("com.microsoft.playready" == this.b.keySystem && 16 == c.byteLength) {
                        var g = new DataView(c), k = g.getUint32(0, !0), l = g.getUint16(4, !0), p = g.getUint16(6, !0);
                        g.setUint32(0, k, !1);
                        g.setUint16(4, l, !1);
                        g.setUint16(6, p, !1)
                    }
                    "com.microsoft.playready" == this.b.keySystem && "status-pending" == a && (a = "usable");
                    "status-pending" !=
                    a && "internal-error" != a && (this.a[b].loaded = !0, this.a.every(function (a) {
                        return a.loaded
                    }) && this.j.resolve());
                    "expired" == a && (d = !0);
                    g = Va(new Uint8Array(c));
                    this.w[g] = a
                }.bind(this));
                c = a.expiration - Date.now();
                if (0 > c || d && 1E3 > c) this.a.splice(b, 1), a.close();
                Sc(this.v)
            }
        };
        h.mc = function () {
            Ra(this.w, function (a, b) {
                return "expired" == b
            }) && this.g(new t(6, 6014));
            this.L(this.w)
        };

        function hd() {
            var a = [], b = {persistentState: "required", sessionTypes: ["persistent-license"]}, c = {};
            "org.w3.clearkey com.widevine.alpha com.microsoft.playready com.apple.fps.2_0 com.apple.fps.1_0 com.apple.fps com.adobe.primetime".split(" ").forEach(function (d) {
                var e = navigator.requestMediaKeySystemAccess(d, [b, {}]).then(function (a) {
                    return a.createMediaKeys()
                }).then(function (a) {
                    var b = !1;
                    try {
                        a.createSession("persistent-license"), b = !0
                    } catch (e) {
                    }
                    c[d] = {persistentState: b}
                }, function () {
                    c[d] = null
                });
                a.push(e)
            });
            return Promise.all(a).then(function () {
                return c
            })
        };

        function id(a, b) {
            if (!a || 1 == a.length && 1E-6 > a.end(0) - a.start(0)) return 0;
            for (var c = 0; c < a.length; ++c) if (b + 1E-4 >= a.start(c) && b < a.end(c)) return a.end(c) - b;
            return 0
        };

        function jd(a, b, c) {
            this.i = a;
            this.f = b;
            this.j = c;
            this.c = {};
            this.b = null;
            this.a = {};
            this.g = new w;
            this.h = !1
        }

        function kd() {
            var a = {};
            'video/mp4; codecs="avc1.42E01E",audio/mp4; codecs="mp4a.40.2",video/webm; codecs="vp8",video/webm; codecs="vp9",audio/webm; codecs="vorbis",audio/webm; codecs="opus",video/mp2t; codecs="avc1.42E01E",video/mp2t; codecs="mp4a.40.2",text/vtt,application/mp4; codecs="wvtt",application/ttml+xml,application/mp4; codecs="stpp"'.split(",").forEach(function (b) {
                a[b] = !!S[b] || MediaSource.isTypeSupported(b);
                var c = b.split(";")[0];
                a[c] = a[c] || a[b]
            });
            return a
        }

        h = jd.prototype;
        h.o = function () {
            this.h = !0;
            var a = [], b;
            for (b in this.a) {
                var c = this.a[b], d = c[0];
                this.a[b] = c.slice(0, 1);
                d && a.push(d.p["catch"](B));
                for (d = 1; d < c.length; ++d) c[d].p["catch"](B), c[d].p.reject()
            }
            this.b && a.push(this.b.o());
            return Promise.all(a).then(function () {
                this.g.o();
                this.b = this.j = this.f = this.i = this.g = null;
                this.c = {};
                this.a = {}
            }.bind(this))
        };
        h.init = function (a) {
            for (var b in a) {
                var c = a[b];
                "text" == b ? this.b = new qc(this.j, c) : (c = this.f.addSourceBuffer(c), x(this.g, c, "error", this.Jc.bind(this, b)), x(this.g, c, "updateend", this.Da.bind(this, b)), this.c[b] = c, this.a[b] = [])
            }
        };

        function ld(a, b) {
            var c;
            "text" == b ? c = a.b.b : (c = md(a, b), c = !c || 1 == c.length && 1E-6 > c.end(0) - c.start(0) ? null : c.length ? c.start(0) : null);
            return c
        }

        function nd(a, b, c) {
            "text" == b ? (b = tc(a.b, c), b || (b = tc(a.b, c + .1)) && (b += .1)) : (a = md(a, b), b = id(a, c), b || (b = id(a, c + .1)) && (b += .1));
            return b
        }

        function md(a, b) {
            try {
                return a.c[b].buffered
            } catch (c) {
                return null
            }
        }

        function od(a, b, c, d, e) {
            return "text" == b ? sc(a.b, c, d, e) : pd(a, b, a.Ic.bind(a, b, c))
        }

        h.remove = function (a, b, c) {
            return "text" == a ? this.b.remove(b, c) : Promise.all([pd(this, a, this.sb.bind(this, a, b, c)), pd(this, a, this.bb.bind(this, a))])
        };
        h.clear = function (a) {
            return "text" == a ? this.b.remove(0, Number.POSITIVE_INFINITY) : pd(this, a, this.sb.bind(this, a, 0, this.f.duration))
        };

        function qd(a, b, c) {
            return "text" == b ? (a.b.h = c, Promise.resolve()) : pd(a, b, a.zc.bind(a, b, c))
        }

        function rd(a, b, c) {
            return "text" == b ? (a.b.f = c, Promise.resolve()) : Promise.all([pd(a, b, a.bb.bind(a, b)), pd(a, b, a.xc.bind(a, b, c))])
        }

        h.endOfStream = function (a) {
            return sd(this, function () {
                a ? this.f.endOfStream(a) : this.f.endOfStream()
            }.bind(this))
        };
        h.xa = function (a) {
            return sd(this, function () {
                this.f.duration = a
            }.bind(this))
        };
        h.X = function () {
            return this.f.duration
        };
        h.Ic = function (a, b) {
            this.c[a].appendBuffer(b)
        };
        h.sb = function (a, b, c) {
            c <= b ? this.Da(a) : this.c[a].remove(b, c)
        };
        h.bb = function (a) {
            var b = this.c[a].appendWindowEnd;
            this.c[a].abort();
            this.c[a].appendWindowEnd = b;
            this.Da(a)
        };
        h.zc = function (a, b) {
            this.c[a].timestampOffset = b;
            this.Da(a)
        };
        h.xc = function (a, b) {
            this.c[a].appendWindowEnd = b + .04;
            this.Da(a)
        };
        h.Jc = function (a) {
            this.a[a][0].p.reject(new t(3, 3014, this.i.error ? this.i.error.code : 0))
        };
        h.Da = function (a) {
            var b = this.a[a][0];
            b && (b.p.resolve(), td(this, a))
        };

        function pd(a, b, c) {
            if (a.h) return Promise.reject();
            c = {start: c, p: new v};
            a.a[b].push(c);
            if (1 == a.a[b].length) try {
                c.start()
            } catch (d) {
                "QuotaExceededError" == d.name ? c.p.reject(new t(3, 3017, b)) : c.p.reject(new t(3, 3015, d)), td(a, b)
            }
            return c.p
        }

        function sd(a, b) {
            if (a.h) return Promise.reject();
            var c = [], d;
            for (d in a.c) {
                var e = new v, f = {
                    start: function (a) {
                        a.resolve()
                    }.bind(null, e), p: e
                };
                a.a[d].push(f);
                c.push(e);
                1 == a.a[d].length && f.start()
            }
            return Promise.all(c).then(function () {
                var a, c;
                try {
                    b()
                } catch (d) {
                    c = Promise.reject(new t(3, 3015, d))
                }
                for (a in this.c) td(this, a);
                return c
            }.bind(a), function () {
                return Promise.reject()
            }.bind(a))
        }

        function td(a, b) {
            a.a[b].shift();
            var c = a.a[b][0];
            if (c) try {
                c.start()
            } catch (d) {
                c.p.reject(new t(3, 3015, d)), td(a, b)
            }
        };

        function T(a) {
            var b = Cc(a);
            a = [];
            var c = new DOMParser, d = null;
            try {
                d = c.parseFromString(b, "text/xml")
            } catch (k) {
                throw new t(2, 2005);
            }
            if (d) {
                var e, f;
                if (b = d.getElementsByTagName("tt")[0]) c = b.getAttribute("ttp:frameRate"), d = b.getAttribute("ttp:subFrameRate"), e = b.getAttribute("ttp:frameRateMultiplier"), f = b.getAttribute("ttp:tickRate"); else throw new t(2, 2006);
                c = new ud(c, d, e, f);
                d = T.b(b.getElementsByTagName("styling")[0]);
                e = T.b(b.getElementsByTagName("layout")[0]);
                b = T.b(b.getElementsByTagName("body")[0]);
                for (f =
                         0; f < b.length; f++) {
                    var g = T.c(b[f], c, d, e);
                    g && a.push(g)
                }
            }
            return a
        }

        T.l = /^(\d{2,}):(\d{2}):(\d{2}):(\d{2})\.?(\d+)?$/;
        T.u = /^(?:(\d{2,}):)?(\d{2}):(\d{2})$/;
        T.m = /^(?:(\d{2,}):)?(\d{2}):(\d{2}\.\d{2,})$/;
        T.v = /^(\d*\.?\d*)f$/;
        T.B = /^(\d*\.?\d*)t$/;
        T.w = /^(?:(\d*\.?\d*)h)?(?:(\d*\.?\d*)m)?(?:(\d*\.?\d*)s)?(?:(\d*\.?\d*)ms)?$/;
        T.j = /^(\d{1,2}|100)% (\d{1,2}|100)%$/;
        T.b = function (a) {
            var b = [];
            if (!a) return b;
            for (var c = a.childNodes, d = 0; d < c.length; d++) {
                var e = "span" == c[d].nodeName && "p" == a.nodeName;
                c[d].nodeType != Node.ELEMENT_NODE || "br" == c[d].nodeName || e || (e = T.b(c[d]), 0 < e.length ? b = b.concat(e) : b.push(c[d]))
            }
            b.length || b.push(a);
            return b
        };
        T.c = function (a, b, c, d) {
            var e = T.a(a.getAttribute("begin"), b), f = T.a(a.getAttribute("end"), b);
            b = T.a(a.getAttribute("dur"), b);
            var g = a.textContent;
            null == f && null != b && (f = e + b);
            if (null == e || null == f) throw new t(2, 2001);
            window.VTTCue ? (e = new VTTCue(e, f, g), a = T.h(a, "region", d), T.la(e, a, c)) : e = new TextTrackCue(e, f, g);
            return e
        };
        T.la = function (a, b, c) {
            var d, e = T.f(b, c, "tts:textAlign");
            e && (a.f = e);
            if (e = T.f(b, c, "tts:extent")) if (d = T.j.exec(e)) a.size = Number(d[1]);
            d = T.f(b, c, "tts:writingMode");
            e = !0;
            "tb" == d || "tblr" == d ? a.b = "lr" : "tbrl" == d ? a.b = "rl" : e = !1;
            if (b = T.f(b, c, "tts:origin")) if (d = T.j.exec(b)) e ? (a.position = Number(d[2]), a.a = Number(d[1])) : (a.position = Number(d[1]), a.a = Number(d[2]))
        };
        T.f = function (a, b, c) {
            for (var d = T.b(a), e = 0; e < d.length; e++) {
                var f = d[e].getAttribute(c);
                if (f) return f
            }
            return (a = T.h(a, "style", b)) ? a.getAttribute(c) : null
        };
        T.h = function (a, b, c) {
            if (!a || 1 > c.length) return null;
            var d = null;
            if (a = T.ma(a, b)) for (b = 0; b < c.length; b++) if (c[b].getAttribute("xml:id") == a) {
                d = c[b];
                break
            }
            return d
        };
        T.ma = function (a, b) {
            for (var c = null; a && !(c = a.getAttribute(b));) a = a.parentNode;
            return c
        };
        T.a = function (a, b) {
            var c = null;
            T.l.test(a) ? c = T.na(b, a) : T.u.test(a) ? c = T.g(T.u, a) : T.m.test(a) ? c = T.g(T.m, a) : T.v.test(a) ? c = T.ya(b, a) : T.B.test(a) ? c = T.za(b, a) : T.w.test(a) && (c = T.g(T.w, a));
            return c
        };
        T.ya = function (a, b) {
            var c = T.v.exec(b);
            return Number(c[1]) / a.a
        };
        T.za = function (a, b) {
            var c = T.B.exec(b);
            return Number(c[1]) / a.b
        };
        T.na = function (a, b) {
            var c = T.l.exec(b), d = Number(c[1]), e = Number(c[2]), f = Number(c[3]), g = Number(c[4]),
                g = g + (Number(c[5]) || 0) / a.c, f = f + g / a.a;
            return f + 60 * e + 3600 * d
        };
        T.g = function (a, b) {
            var c = a.exec(b);
            return c && "" != c[0] ? (Number(c[4]) || 0) / 1E3 + (Number(c[3]) || 0) + 60 * (Number(c[2]) || 0) + 3600 * (Number(c[1]) || 0) : null
        };

        function ud(a, b, c, d) {
            this.a = Number(a) || 30;
            this.c = Number(b) || 1;
            this.b = Number(d);
            this.b || (this.b = a ? this.a * this.c : 1);
            c && (a = /^(\d+) (\d+)$/g.exec(c)) && (this.a *= a[1] / a[2])
        }

        S["application/ttml+xml"] = T;

        function vd(a) {
            var b = new Fb(new DataView(a)), c = Nb(1835295092, b);
            if (-1 != c) return T(Mb(b, c - 8).buffer);
            if (-1 != Ob(a, vd.L)) return [];
            throw new t(2, 2007);
        }

        vd.L = 1937010800;
        S['application/mp4; codecs="stpp"'] = vd;

        function wd(a) {
            this.b = a;
            this.a = 0
        }

        function xd(a, b) {
            var c;
            b.lastIndex = a.a;
            c = (c = b.exec(a.b)) ? {position: c.index, length: c[0].length, uc: c} : null;
            if (a.a == a.b.length || !c || c.position != a.a) return null;
            a.a += c.length;
            return c.uc
        }

        function yd(a) {
            return a.a == a.b.length ? null : (a = xd(a, /[^ \t\n]*/gm)) ? a[0] : null
        };

        function U(a) {
            a = Cc(a);
            a = a.replace(/\r\n|\r(?=[^\n]|$)/gm, "\n");
            a = a.split(/\n{2,}/m);
            if (!/^WEBVTT($|[ \t\n])/m.test(a[0])) throw new t(2, 2E3);
            for (var b = [], c = 1; c < a.length; c++) {
                var d = U.c(a[c].split("\n"));
                d && b.push(d)
            }
            return b
        }

        U.c = function (a) {
            if (1 == a.length && !a[0] || /^NOTE($|[ \t])/.test(a[0])) return null;
            var b = null;
            0 > a[0].indexOf("--\x3e") && (b = a[0], a.splice(0, 1));
            var c = new wd(a[0]), d = U.a(c), e = xd(c, /[ \t]+--\x3e[ \t]+/g), f = U.a(c);
            if (null == d || !e || null == f) throw new t(2, 2001);
            a = a.slice(1).join("\n");
            if (window.VTTCue) for (d = new VTTCue(d, f, a), xd(c, /[ \t]+/gm), f = yd(c); f;) {
                if (!U.i(d, f)) throw new t(2, 2002);
                xd(c, /[ \t]+/gm);
                f = yd(c)
            } else d = new TextTrackCue(d, f, a);
            null != b && (d.id = b);
            return d
        };
        U.i = function (a, b) {
            var c;
            if (c = /^align:(start|middle|end|left|right)$/.exec(b)) a.align = c[1]; else if (c = /^vertical:(lr|rl)$/.exec(b)) a.b = c[1]; else if (c = /^size:(\d{1,2}|100)%$/.exec(b)) a.size = Number(c[1]); else if (c = /^position:(\d{1,2}|100)%$/.exec(b)) a.position = Number(c[1]); else if (c = /^line:(\d{1,2}|100)%$/.exec(b)) a.c = !1, a.a = Number(c[1]); else if (c = /^line:(-?\d+)$/.exec(b)) a.c = !0, a.a = Number(c[1]); else return !1;
            return !0
        };
        U.a = function (a) {
            a = xd(a, /(?:(\d{2,}):)?(\d{2}):(\d{2})\.(\d{3})/g);
            if (!a) return null;
            var b = Number(a[2]), c = Number(a[3]);
            return 59 < b || 59 < c ? null : Number(a[4]) / 1E3 + c + 60 * b + 3600 * (Number(a[1]) || 0)
        };
        S["text/vtt"] = U;

        function V(a, b, c) {
            var d = new Fb(new DataView(a)), e = Nb(1835295092, d);
            if (-1 != e) return V.oa(Mb(d, e - 8).buffer, b, c);
            if (-1 != Ob(a, V.ka)) return [];
            throw new t(2, 2008);
        }

        V.oa = function (a, b, c) {
            a = new Fb(new DataView(a));
            for (var d = []; Hb(a);) {
                var e = Nb(V.ja, a);
                if (-1 == e) break;
                e = V.c(Mb(a, e - 8).buffer, b, c);
                d.push(e)
            }
            return d
        };
        V.c = function (a, b, c) {
            a = new Fb(new DataView(a));
            for (var d, e, f; Hb(a);) {
                var g = L(a), k = L(a), l = Cc(Mb(a, g - 8).buffer);
                1 == g && Lb(a);
                switch (k) {
                    case V.G:
                        d = l;
                        break;
                    case V.F:
                        f = l;
                        break;
                    case V.W:
                        e = l
                }
            }
            if (d) if (window.VTTCue) {
                if (b = new VTTCue(b, c, d), f && (b.id = f), e) for (e = new wd(e), f = yd(e); f;) {
                    if (!U.i(b, f)) throw new t(2, 2002);
                    xd(e, /[ \t]+/gm);
                    f = yd(e)
                }
            } else b = new TextTrackCue(Number(b), Number(c), d); else throw new t(2, 2008);
            return b
        };
        V.ka = 2004251764;
        V.ja = 1987343459;
        V.G = 1885436268;
        V.F = 1768187247;
        V.W = 1937011815;
        S['application/mp4; codecs="wvtt"'] = V;

        function zd(a, b, c, d, e, f) {
            this.a = a;
            this.c = b;
            this.i = c;
            this.m = d;
            this.j = e;
            this.l = f;
            this.b = new w;
            this.h = !1;
            this.g = 1;
            this.f = null;
            0 < a.readyState ? this.kb() : x(this.b, a, "loadedmetadata", this.kb.bind(this));
            x(this.b, a, "ratechange", this.$b.bind(this))
        }

        h = zd.prototype;
        h.o = function () {
            var a = this.b.o();
            this.b = null;
            null != this.f && (window.clearInterval(this.f), this.f = null);
            this.l = this.j = this.c = this.a = null;
            return a
        };

        function Ad(a) {
            return 0 < a.a.readyState ? Bd(a, a.a.currentTime) : Cd(a)
        }

        function Cd(a) {
            return a.m ? Bd(a, a.m) : a.c.X() < Number.POSITIVE_INFINITY ? a.c.pa() : Math.max(a.c.Y() - a.i, a.c.pa())
        }

        function Dd(a, b) {
            b != a.h && (a.h = b, Ed(a, a.g), a.j(b))
        }

        h.Oa = function () {
            return this.g
        };

        function Ed(a, b) {
            null != a.f && (window.clearInterval(a.f), a.f = null);
            a.g = b;
            a.a.playbackRate = a.h || 0 > b ? 0 : b;
            !a.h && 0 > b && (a.f = window.setInterval(function () {
                this.a.currentTime += b / 4
            }.bind(a), 250))
        }

        h.$b = function () {
            this.a.playbackRate != (this.h || 0 > this.g ? 0 : this.g) && Ed(this, this.a.playbackRate)
        };
        h.kb = function () {
            this.b.ha(this.a, "loadedmetadata");
            var a = Cd(this);
            .001 > Math.abs(this.a.currentTime - a) ? (x(this.b, this.a, "seeking", this.mb.bind(this)), x(this.b, this.a, "playing", this.lb.bind(this))) : (x(this.b, this.a, "seeking", this.bc.bind(this)), this.a.currentTime = a)
        };
        h.bc = function () {
            this.b.ha(this.a, "seeking");
            x(this.b, this.a, "seeking", this.mb.bind(this));
            x(this.b, this.a, "playing", this.lb.bind(this))
        };
        h.mb = function () {
            var a = this.a.currentTime, b = Fd(this, a);
            .001 < Math.abs(b - a) ? Gd(this, a, b) : this.l()
        };
        h.lb = function () {
            var a = this.a.currentTime, b = Fd(this, a);
            .001 < Math.abs(b - a) && Gd(this, a, b)
        };

        function Fd(a, b) {
            var c = a.c, d = c.pa(), e = c.Y();
            if (!c.T() || c.a == Number.POSITIVE_INFINITY) return b < d ? d : b > e ? e : b;
            c = d + 1;
            d = c + a.i;
            return b >= d && b <= e || id(a.a.buffered, b) && b >= c && b <= e ? b : b > e ? e : e < d && b >= c && b <= e ? b : Math.min(d + 2, e)
        }

        function Gd(a, b, c) {
            a.a.currentTime = c;
            var d = 0, e = function () {
                !this.a || 10 <= d++ || this.a.currentTime != b || (this.a.currentTime = c, setTimeout(e, 100))
            }.bind(a);
            setTimeout(e, 100)
        }

        function Bd(a, b) {
            var c = a.c.pa();
            if (b < c) return c;
            c = a.c.Y();
            return b > c ? c : b
        };

        function Hd(a, b, c, d, e, f, g, k, l) {
            this.h = a;
            this.c = b;
            this.L = c;
            this.a = d;
            this.G = e;
            this.v = f;
            this.j = g;
            this.w = k || null;
            this.B = l || null;
            this.m = null;
            this.i = 1;
            this.F = Promise.resolve();
            this.g = [];
            this.l = {};
            this.b = {};
            this.f = this.u = !1
        }

        Hd.prototype.o = function () {
            for (var a in this.b) Id(this.b[a]);
            this.m = this.b = this.l = this.g = this.B = this.w = this.j = this.v = this.G = this.F = this.a = this.L = this.c = this.h = null;
            this.f = !0;
            return Promise.resolve()
        };
        Hd.prototype.configure = function (a) {
            this.m = a;
            this.h.i = this.i * Math.max(this.a.minBufferTime || 0, this.m.rebufferingGoal)
        };
        Hd.prototype.init = function () {
            var a = this.G(this.a.periods[Jd(this, Ad(this.h))]);
            return Pa(a) ? Promise.reject(new t(5, 5005)) : Kd(this, a).then(function () {
                this.w && this.w()
            }.bind(this))
        };

        function Ld(a) {
            return a.a.periods[Jd(a, Ad(a.h))]
        }

        function Md(a) {
            return Qa(a.b, function (a) {
                return a.stream
            })
        }

        function Nd(a, b) {
            var c = {};
            c.text = b;
            return Kd(a, c)
        }

        function Od(a, b, c, d) {
            if (b = a.b[b]) {
                var e = a.g[Pd(a, c)];
                e && e.va && (e = a.l[c.id]) && e.va && b.stream != c && (b.stream = c, b.Ha = !0, void 0 == d || b.ba || b.ca || (b.ea ? Qd(b, d) : (Id(b), Rd(a, b, d))))
            }
        }

        function Qd(a, b) {
            a.ba ? a.Ga = Math.min(a.Ga, b) : (a.ba = !0, a.Ga = b)
        }

        function Kd(a, b) {
            var c = Jd(a, Ad(a.h)), d = Qa(b, function (a) {
                return a.mimeType + (a.codecs ? '; codecs="' + a.codecs + '"' : "")
            });
            a.c.init(d);
            Sd(a);
            d = C(b);
            return Td(a, d).then(function () {
                if (!this.f) for (var a in b) {
                    var d = b[a];
                    this.b[a] || (this.b[a] = {
                        stream: d,
                        type: a,
                        ra: null,
                        U: null,
                        Ha: !0,
                        ta: !1,
                        Ia: c,
                        endOfStream: !1,
                        ea: !1,
                        aa: null,
                        ba: !1,
                        Ga: 0,
                        ca: !1,
                        Va: !1
                    }, Ud(this, this.b[a], 0))
                }
            }.bind(a))
        }

        function Vd(a, b) {
            var c = a.g[b];
            if (c) return c.H;
            c = {H: new v, va: !1};
            a.g[b] = c;
            var d = a.a.periods[b].streamSets.map(function (a) {
                return a.streams
            }).reduce(A, []);
            a.F = a.F.then(function () {
                if (!this.f) return Td(this, d)
            }.bind(a)).then(function () {
                this.f || (this.g[b].H.resolve(), this.g[b].va = !0)
            }.bind(a))["catch"](function (a) {
                this.f || (this.g[b].H.reject(), delete this.g[b], this.j(a))
            }.bind(a));
            return c.H
        }

        function Td(a, b) {
            for (var c = [], d = 0; d < b.length; ++d) {
                var e = b[d], f = a.l[e.id];
                f ? c.push(f.H) : (a.l[e.id] = {H: new v, va: !1}, c.push(e.createSegmentIndex()))
            }
            return Promise.all(c).then(function () {
                if (!this.f) for (var a = 0; a < b.length; ++a) {
                    var c = this.l[b[a].id];
                    c.va || (c.H.resolve(), c.va = !0)
                }
            }.bind(a))["catch"](function (a) {
                if (!this.f) return this.l[e.id].H.reject(), delete this.l[e.id], Promise.reject(a)
            }.bind(a))
        }

        function Sd(a) {
            var b = a.a.presentationTimeline.X();
            b < Number.POSITIVE_INFINITY ? a.c.xa(b) : a.c.xa(Math.pow(2, 32))
        }

        Hd.prototype.W = function (a) {
            if (!this.f && !a.ea && null != a.aa && !a.ca) if (a.aa = null, a.ba) Rd(this, a, a.Ga); else {
                try {
                    var b = Wd(this, a);
                    null != b && Ud(this, a, b)
                } catch (c) {
                    this.j(c);
                    return
                }
                b = C(this.b);
                Dd(this.h, b.some(function (a) {
                    return a.ta
                }));
                Xd(this, a);
                b.every(function (a) {
                    return a.endOfStream
                }) && this.c.endOfStream()
            }
        };

        function Wd(a, b) {
            var c = Ad(a.h), d = nd(a.c, b.type, c), e = a.i * Math.max(a.a.minBufferTime || 0, a.m.rebufferingGoal);
            if (d >= Math.max(e, a.i * a.m.bufferingGoal)) return b.ta = !1, .5;
            var f;
            f = a.c;
            var g = b.type;
            "text" == g ? f = f.b.a : (f = md(f, g), f = !f || 1 == f.length && 1E-6 > f.end(0) - f.start(0) ? null : f.length ? f.end(f.length - 1) : null);
            g = b.ra && b.U ? a.a.periods[Pd(a, b.ra)].startTime + b.U.endTime : c;
            if (g >= a.a.presentationTimeline.X()) return b.ta = !1, b.endOfStream = !0, null;
            b.endOfStream = !1;
            !a.u && d < e || 1 >= d ? b.ta = !0 : d >= e && (b.ta = !1);
            d = Pd(a, b.stream);
            e = Jd(a, g);
            if (e != d) return b.Ia = e, null;
            b.U && b.stream == b.ra ? (e = b.U.position + 1, e = Yd(a, b, d, e)) : (e = b.U ? b.stream.findSegmentPosition(Math.max(0, a.a.periods[Pd(a, b.ra)].startTime + b.U.endTime - a.a.periods[d].startTime)) : b.stream.findSegmentPosition(Math.max(0, (f || c) - a.a.periods[d].startTime)), null == e ? e = null : (g = null, null == f && (g = Yd(a, b, d, Math.max(0, e - 1))), e = g || Yd(a, b, d, e)));
            if (!e) return 1;
            Zd(a, b, c, d, e);
            return null
        }

        function Yd(a, b, c, d) {
            c = a.a.periods[c];
            b = b.stream.getSegmentReference(d);
            if (!b) return null;
            a = a.a.presentationTimeline;
            d = a.Y();
            return c.startTime + b.endTime < a.qa() || c.startTime + b.startTime > d ? null : b
        }

        function Zd(a, b, c, d, e) {
            var f = a.a.periods[d], g = b.stream, k = a.a.periods[d + 1], l = null,
                l = k ? k.startTime : a.a.presentationTimeline.X();
            d = $d(a, b, d, l);
            b.ea = !0;
            b.Ha = !1;
            k = ae(a, e);
            Promise.all([d, k]).then(function (a) {
                if (!this.f) return be(this, b, c, f, g, e, a[1])
            }.bind(a)).then(function () {
                this.f || (b.ea = !1, b.Va = !1, Ud(this, b, 0), ce(this, g))
            }.bind(a))["catch"](function (a) {
                this.f || (b.ea = !1, 1001 == a.code || 1002 == a.code || 1003 == a.code ? (this.j(a), Ud(this, b, 4)) : 3017 == a.code ? de(this, b, a) : this.j(a))
            }.bind(a))
        }

        function de(a, b, c) {
            if (!C(a.b).some(function (a) {
                return a != b && a.Va
            })) {
                var d = Math.round(100 * a.i);
                if (20 < d) a.i -= .2; else if (4 < d) a.i -= .04; else {
                    a.j(c);
                    return
                }
                b.Va = !0
            }
            Ud(a, b, b.ta ? 0 : 4)
        }

        function $d(a, b, c, d) {
            if (!b.Ha) return Promise.resolve();
            c = qd(a.c, b.type, a.a.periods[c].startTime - b.stream.presentationTimeOffset);
            d = null != d ? rd(a.c, b.type, d) : Promise.resolve();
            if (!b.stream.initSegmentReference) return Promise.all([c, d]);
            a = ae(a, b.stream.initSegmentReference).then(function (a) {
                if (!this.f) return od(this.c, b.type, a, null, null)
            }.bind(a))["catch"](function (a) {
                b.Ha = !0;
                return Promise.reject(a)
            });
            return Promise.all([c, d, a])
        }

        function be(a, b, c, d, e, f, g) {
            return ee(a, b, c).then(function () {
                if (!this.f) return od(this.c, b.type, g, f.startTime + d.startTime, f.endTime + d.startTime)
            }.bind(a)).then(function () {
                if (!this.f) return b.ra = e, b.U = f, Promise.resolve()
            }.bind(a))
        }

        function ee(a, b, c) {
            var d = ld(a.c, b.type);
            if (null == d) return Promise.resolve();
            c = c - d - a.m.bufferBehind;
            return 0 >= c ? Promise.resolve() : a.c.remove(b.type, d, d + c).then(function () {
            }.bind(a))
        }

        function ce(a, b) {
            if (!a.u && (a.u = C(a.b).every(function (a) {
                return !a.ba && !a.ca && a.U
            }), a.u)) {
                var c = Pd(a, b);
                a.g[c] || Vd(a, c).then(function () {
                    this.v()
                }.bind(a))["catch"](B);
                for (c = 0; c < a.a.periods.length; ++c) Vd(a, c)["catch"](B);
                a.B && a.B()
            }
        }

        function Xd(a, b) {
            if (b.Ia != Pd(a, b.stream)) {
                var c = b.Ia, d = C(a.b);
                d.every(function (a) {
                    return a.Ia == c
                }) && d.every(fe) && Vd(a, c).then(function () {
                    if (!this.f) {
                        var a = this.G(this.a.periods[c]), b;
                        for (b in this.b) if (!a[b]) {
                            this.j(new t(5, 5005));
                            return
                        }
                        for (b in a) if (!this.b[b]) {
                            this.j(new t(5, 5005));
                            return
                        }
                        for (b in this.b) {
                            Od(this, b, a[b]);
                            var d = this.b[b];
                            fe(d) && Ud(this, d, 0)
                        }
                        this.v()
                    }
                }.bind(a))["catch"](B)
            }
        }

        function fe(a) {
            return !a.ea && null == a.aa && !a.ba && !a.ca
        }

        function Jd(a, b) {
            for (var c = a.a.periods.length - 1; 0 < c; --c) if (b >= a.a.periods[c].startTime) return c;
            return 0
        }

        function Pd(a, b) {
            for (var c = 0; c < a.a.periods.length; ++c) for (var d = a.a.periods[c],
                                                                  e = 0; e < d.streamSets.length; ++e) if (0 <= d.streamSets[e].streams.indexOf(b)) return c;
            return -1
        }

        function ae(a, b) {
            var c = lc(b.a(), a.m.retryParameters);
            if (b.K || null != b.C) {
                var d = "bytes=" + b.K + "-";
                null != b.C && (d += b.C);
                c.headers.Range = d
            }
            return a.L.request(1, c).then(function (a) {
                return a.data
            })
        }

        function Rd(a, b, c) {
            b.ba = !1;
            b.ca = !0;
            var d = Ad(a.h) + c, e = a.c.X();
            (c ? a.c.remove(b.type, d, e) : a.c.clear(b.type)).then(function () {
                this.f || (b.ra = null, b.U = null, b.ca = !1, Ud(this, b, 0))
            }.bind(a))
        }

        function Ud(a, b, c) {
            b.aa = window.setTimeout(a.W.bind(a, b), 1E3 * c)
        }

        function Id(a) {
            null != a.aa && (window.clearTimeout(a.aa), a.aa = null)
        };
        jc.data = function (a) {
            return new Promise(function (b) {
                var c = a.split(":");
                if (2 > c.length || "data" != c[0]) throw new t(1, 1004, a);
                c = c.slice(1).join(":").split(",");
                if (2 > c.length) throw new t(1, 1004, a);
                var d = c[0], c = window.decodeURIComponent(c.slice(1).join(",")), d = d.split(";"), e = null;
                1 < d.length && (e = d[1]);
                if ("base64" == e) c = Ta(c).buffer; else {
                    if (e) throw new t(1, 1005, a);
                    c = Dc(c)
                }
                b({uri: a, data: c, headers: {}})
            })
        };

        function ge(a, b) {
            return new Promise(function (c, d) {
                var e = new XMLHttpRequest;
                e.open(b.method, a, !0);
                e.responseType = "arraybuffer";
                e.timeout = b.retryParameters.timeout;
                e.withCredentials = b.allowCrossSiteCredentials;
                e.onload = function (b) {
                    b = b.target;
                    if (200 <= b.status && 299 >= b.status) {
                        var e = b.getAllResponseHeaders().split("\r\n").reduce(function (a, b) {
                            var c = b.split(": ");
                            a[c[0].toLowerCase()] = c.slice(1).join(": ");
                            return a
                        }, {});
                        b.tc && (a = b.tc);
                        c({uri: a, data: b.response, headers: e})
                    } else {
                        e = null;
                        try {
                            e = Cc(b.response)
                        } catch (f) {
                        }
                        d(new t(1,
                            1001, a, b.status, e))
                    }
                };
                e.onerror = function () {
                    d(new t(1, 1002, a))
                };
                e.ontimeout = function () {
                    d(new t(1, 1003, a))
                };
                for (var f in b.headers) e.setRequestHeader(f, b.headers[f]);
                e.send(b.body)
            })
        }

        jc.http = ge;
        jc.https = ge;

        function he() {
            this.a = null;
            this.c = [];
            this.b = {}
        }

        h = he.prototype;
        h.init = function (a) {
            if (!window.indexedDB) return Promise.reject(new t(9, 9E3));
            var b = window.indexedDB.open("shaka_offline_db", 1), c = new v;
            b.onupgradeneeded = function (b) {
                b = b.target.result;
                for (var c in a) b.createObjectStore(c, {keyPath: a[c]})
            };
            b.onsuccess = function (a) {
                this.a = a.target.result;
                c.resolve()
            }.bind(this);
            b.onerror = ie.bind(null, b, c);
            return c.then(function () {
                var b = Object.keys(a);
                return Promise.all(b.map(function (a) {
                    return je(this, a).then(function (b) {
                        this.b[a] = b
                    }.bind(this))
                }.bind(this)))
            }.bind(this))
        };
        h.o = function () {
            return Promise.all(this.c.map(function (a) {
                try {
                    a.transaction.abort()
                } catch (b) {
                }
                return a.H["catch"](B)
            })).then(function () {
                this.a && (this.a.close(), this.a = null)
            }.bind(this))
        };
        h.get = function (a, b) {
            return ke(this, a, "readonly", function (a) {
                return a.get(b)
            })
        };
        h.forEach = function (a, b) {
            return ke(this, a, "readonly", function (a) {
                return a.openCursor()
            }, function (a) {
                a && (b(a.value), a["continue"]())
            })
        };

        function le(a, b, c) {
            return ke(a, b, "readwrite", function (a) {
                return a.put(c)
            })
        }

        h.remove = function (a, b) {
            return ke(this, a, "readwrite", function (a) {
                return a["delete"](b)
            })
        };

        function me(a, b) {
            var c = [];
            return ke(a, "segment", "readwrite", function (a) {
                return a.openCursor()
            }, function (a) {
                if (a) {
                    if (b(a.value)) {
                        var e = a["delete"](), f = new v;
                        e.onsuccess = f.resolve;
                        e.onerror = ie.bind(null, e, f);
                        c.push(f)
                    }
                    a["continue"]()
                }
            }).then(function () {
                return Promise.all(c)
            }).then(function () {
                return c.length
            })
        }

        function je(a, b) {
            var c = 0;
            return ke(a, b, "readonly", function (a) {
                return a.openCursor(null, "prev")
            }, function (a) {
                a && (c = a.key + 1)
            }).then(function () {
                return c
            })
        }

        function ke(a, b, c, d, e) {
            c = a.a.transaction([b], c);
            var f = d(c.objectStore(b)), g = new v;
            e && (f.onsuccess = function (a) {
                e(a.target.result)
            });
            f.onerror = ie.bind(null, f, g);
            var k = {transaction: c, H: g};
            a.c.push(k);
            var l = function () {
                this.c.splice(this.c.indexOf(k), 1)
            }.bind(a);
            c.oncomplete = function () {
                l();
                g.resolve(f.result)
            };
            c.onerror = function (a) {
                l();
                ie(f, g, a)
            };
            return g
        }

        function ie(a, b, c) {
            "AbortError" == a.error.name ? b.reject(new t(9, 9002)) : b.reject(new t(9, 9001, a.error));
            c.preventDefault()
        };
        var ne = {manifest: "key", segment: "key"};

        function oe(a) {
            return {
                offlineUri: "offline:" + a.key,
                originalManifestUri: a.originalManifestUri,
                duration: a.duration,
                size: a.size,
                tracks: a.periods[0].streams.map(function (a) {
                    return {
                        id: a.id,
                        active: !1,
                        type: a.contentType,
                        bandwidth: 0,
                        language: a.language,
                        kind: a.kind || null,
                        width: a.width,
                        height: a.height
                    }
                }),
                appMetadata: a.appMetadata
            }
        };

        function pe(a, b, c) {
            this.b = {};
            this.i = c;
            this.m = a;
            this.l = b;
            this.j = this.a = null;
            this.f = this.g = this.h = this.c = 0
        }

        pe.prototype.o = function () {
            var a = this.j || Promise.resolve();
            this.b = {};
            this.j = this.a = this.l = this.m = this.i = null;
            return a
        };

        function qe(a, b, c, d, e) {
            a.b[b] = a.b[b] || [];
            a.b[b].push({uris: c.a(), K: c.K, C: c.C, cb: d, Aa: e})
        }

        function re(a, b) {
            a.c = 0;
            a.h = 0;
            a.g = 0;
            a.f = 0;
            C(a.b).forEach(function (a) {
                a.forEach(function (a) {
                    null != a.C ? this.c += a.C - a.K + 1 : this.g += a.cb
                }.bind(this))
            }.bind(a));
            a.a = b;
            a.a.size = a.c;
            var c = C(a.b).map(function (a) {
                var b = 0, c = function () {
                    if (!this.i) return Promise.reject(new t(9, 9002));
                    if (b >= a.length) return Promise.resolve();
                    var g = a[b++];
                    return se(this, g).then(c)
                }.bind(this);
                return c()
            }.bind(a));
            a.b = {};
            return a.j = Promise.all(c)
        }

        function se(a, b) {
            var c = lc(b.uris, a.l);
            if (b.K || null != b.C) c.headers.Range = "bytes=" + b.K + "-" + (null == b.C ? "" : b.C);
            var d;
            return a.m.request(1, c).then(function (a) {
                if (!this.a) return Promise.reject(new t(9, 9002));
                d = a.data.byteLength;
                return b.Aa(a.data)
            }.bind(a)).then(function () {
                if (!this.a) return Promise.reject(new t(9, 9002));
                null == b.C ? (this.a.size += d, this.f += b.cb) : this.h += d;
                var a = (this.h + this.f) / (this.c + this.g), c = oe(this.a);
                this.i.progressCallback(c, a)
            }.bind(a))
        };

        function te() {
        }

        te.prototype.configure = function () {
        };
        te.prototype.start = function (a) {
            var b = /^offline:([0-9]+)$/.exec(a);
            if (!b) return Promise.reject(new t(1, 9004, a));
            var c = Number(b[1]), d = new he;
            return d.init(ne).then(function () {
                return d.get("manifest", c)
            }).then(function (a) {
                if (!a) throw new t(9, 9003, c);
                return ue(a)
            }).then(function (a) {
                return d.o().then(function () {
                    return a
                })
            }, function (a) {
                return d.o().then(function () {
                    throw a;
                })
            })
        };
        te.prototype.stop = function () {
            return Promise.resolve()
        };

        function ue(a) {
            var b = new R(null, 0);
            b.xa(a.duration);
            var c = a.drmInfo ? [a.drmInfo] : [];
            return {
                presentationTimeline: b,
                minBufferTime: 10,
                offlineSessionIds: a.sessionIds,
                periods: a.periods.map(function (a) {
                    return {
                        startTime: a.startTime, streamSets: a.streams.map(function (e) {
                            var f = e.segments.map(function (a, b) {
                                return new H(b, a.startTime, a.endTime, function () {
                                    return [a.uri]
                                }, 0, null)
                            });
                            b.Ca(a.startTime, f);
                            f = new O(f);
                            return {
                                language: e.language, type: e.contentType, primary: e.primary, drmInfos: c, streams: [{
                                    id: e.id,
                                    createSegmentIndex: Promise.resolve.bind(Promise),
                                    findSegmentPosition: f.find.bind(f),
                                    getSegmentReference: f.get.bind(f),
                                    initSegmentReference: e.initSegmentUri ? new zb(function () {
                                        return [e.initSegmentUri]
                                    }, 0, null) : null,
                                    presentationTimeOffset: e.presentationTimeOffset,
                                    mimeType: e.mimeType,
                                    codecs: e.codecs,
                                    bandwidth: 0,
                                    width: e.width || void 0,
                                    height: e.height || void 0,
                                    kind: e.kind,
                                    encrypted: e.encrypted,
                                    keyId: e.keyId,
                                    allowedByApplication: !0,
                                    allowedByKeySystem: !0
                                }]
                            }
                        })
                    }
                })
            }
        }

        mc["application/x-offline-manifest"] = te;
        jc.offline = function (a) {
            if (/^offline:([0-9]+)$/.exec(a)) {
                var b = {uri: a, data: new ArrayBuffer(0), headers: {"content-type": "application/x-offline-manifest"}};
                return Promise.resolve(b)
            }
            if (b = /^offline:[0-9]+\/[0-9]+\/([0-9]+)$/.exec(a)) {
                var c = Number(b[1]), d = new he;
                return d.init(ne).then(function () {
                    return d.get("segment", c)
                }).then(function (b) {
                    return d.o().then(function () {
                        if (!b) throw new t(9, 9003, c);
                        return {uri: a, data: b.data, headers: {}}
                    })
                })
            }
            return Promise.reject(new t(1, 9004, a))
        };

        function ve() {
            this.a = Promise.resolve();
            this.c = this.b = this.f = !1;
            this.g = new Promise(function (a) {
                this.h = a
            }.bind(this))
        }

        ve.prototype.then = function (a) {
            this.a = this.a.then(a).then(function (a) {
                return this.c ? (this.h(), Promise.reject(this.i)) : Promise.resolve(a)
            }.bind(this));
            return this
        };

        function we(a) {
            a.f || (a.a = a.a.then(function (a) {
                this.b = !0;
                return Promise.resolve(a)
            }.bind(a), function (a) {
                this.b = !0;
                return Promise.reject(a)
            }.bind(a)));
            a.f = !0;
            return a.a
        }

        function xe(a, b) {
            if (a.b) return Promise.resolve();
            a.c = !0;
            a.i = b;
            return a.g
        };

        function ye(a, b, c, d, e) {
            var f = e in d, g;
            for (g in b) {
                var k = e + "." + g, l = f ? d[e] : c[g], p = !!{".abr.manager": !0}[k];
                if (f || g in a) void 0 === b[g] ? void 0 === l || f ? delete a[g] : a[g] = l : p ? a[g] = b[g] : "object" == typeof a[g] && "object" == typeof b[g] ? ye(a[g], b[g], l, d, k) : typeof b[g] == typeof l && (a[g] = b[g])
            }
        };

        function ze(a, b, c) {
            var d = !1;
            a.streamSets.forEach(function (a) {
                a.streams.forEach(function (f) {
                    var g = f.allowedByApplication;
                    f.allowedByApplication = !0;
                    if ("video" == a.type) {
                        if (f.width < b.minWidth || f.width > b.maxWidth || f.width > c.width || f.height < b.minHeight || f.height > b.maxHeight || f.height > c.height || f.width * f.height < b.minPixels || f.width * f.height > b.maxPixels || f.bandwidth < b.minVideoBandwidth || f.bandwidth > b.maxVideoBandwidth) f.allowedByApplication = !1
                    } else "audio" == a.type && (f.bandwidth < b.minAudioBandwidth || f.bandwidth >
                        b.maxAudioBandwidth) && (f.allowedByApplication = !1);
                    g != f.allowedByApplication && (d = !0)
                })
            });
            return d
        }

        function Ae(a, b, c) {
            var d = "", e = null;
            a && a.B && (d = a.keySystem(), e = a.m);
            for (a = 0; a < c.streamSets.length; ++a) {
                var f = c.streamSets[a];
                if (d && f.drmInfos.length && !f.drmInfos.some(function (a) {
                    return a.keySystem == d
                })) c.streamSets.splice(a, 1), --a; else {
                    for (var g = b[f.type], k = 0; k < f.streams.length; ++k) {
                        var l = f.streams[k], p = l.mimeType;
                        l.codecs && (p += '; codecs="' + l.codecs + '"');
                        S[p] || MediaSource.isTypeSupported(p) ? e && l.encrypted && 0 > e.indexOf(p) ? (f.streams.splice(k, 1), --k) : !g || "text" == f.type || l.mimeType == g.mimeType && l.codecs.split(".")[0] ==
                            g.codecs.split(".")[0] || (f.streams.splice(k, 1), --k) : (f.streams.splice(k, 1), --k)
                    }
                    f.streams.length || (c.streamSets.splice(a, 1), --a)
                }
            }
        }

        function Be(a, b) {
            return a.streamSets.map(function (a) {
                var d = b ? b[a.type] : null;
                return a.streams.filter(function (a) {
                    return a.allowedByApplication && a.allowedByKeySystem
                }).map(function (b) {
                    return {
                        id: b.id,
                        active: d == b,
                        type: a.type,
                        bandwidth: b.bandwidth,
                        language: a.language,
                        kind: b.kind || null,
                        width: b.width || null,
                        height: b.height || null
                    }
                })
            }).reduce(A, [])
        }

        function Ce(a, b) {
            for (var c = 0; c < a.streamSets.length; c++) for (var d = a.streamSets[c],
                                                                   e = 0; e < d.streams.length; e++) {
                var f = d.streams[e];
                if (f.id == b.id) return {stream: f, Lc: d}
            }
            return null
        }

        function De(a) {
            return a.streams.some(function (a) {
                return a.allowedByApplication && a.allowedByKeySystem
            })
        }

        function Ee(a, b, c) {
            var d = {};
            a.streamSets.forEach(function (a) {
                !De(a) || a.type in d || (d[a.type] = a)
            });
            a.streamSets.forEach(function (a) {
                De(a) && a.primary && (d[a.type] = a)
            });
            [wc, vc, 0].forEach(function (e) {
                a.streamSets.forEach(function (a) {
                    if (De(a)) {
                        var g;
                        "audio" == a.type ? g = b.preferredAudioLanguage : "text" == a.type && (g = b.preferredTextLanguage);
                        if (g) {
                            g = xc(g);
                            var k = xc(a.language);
                            uc(e, g, k) && (d[a.type] = a, c && (c[a.type] = !0))
                        }
                    }
                })
            });
            return d
        };

        function W(a, b) {
            n.call(this);
            this.w = !1;
            this.f = a;
            this.m = null;
            this.v = new w;
            this.ab = new ja;
            this.la = this.c = this.l = this.b = this.i = this.ma = this.G = this.B = this.g = this.h = null;
            this.Ab = 1E9;
            this.ka = [];
            this.Ma = !1;
            this.oa = !0;
            this.j = null;
            this.u = {};
            this.a = Fe(this);
            this.za = {width: Infinity, height: Infinity};
            this.F = [];
            this.ja = this.L = this.na = 0;
            b && b(this);
            this.h = new Q(this.Dc.bind(this));
            this.ma = Ge(this);
            for (var c = 0; c < this.f.textTracks.length; ++c) {
                var d = this.f.textTracks[c];
                d.mode = "disabled";
                "Shaka Player TextTrack" == d.label &&
                (this.m = d)
            }
            this.m || (this.m = this.f.addTextTrack("subtitles", "Shaka Player TextTrack"));
            this.m.mode = "hidden";
            x(this.v, this.f, "error", this.dc.bind(this))
        }

        ba(W);
        m("shaka.Player", W);
        W.prototype.o = function () {
            this.w = !0;
            var a = Promise.resolve();
            this.j && (a = xe(this.j, new t(7, 7E3)));
            return a.then(function () {
                var a = Promise.all([He(this), this.v ? this.v.o() : null, this.h ? this.h.o() : null]);
                this.a = this.h = this.ab = this.v = this.m = this.f = null;
                return a
            }.bind(this))
        };
        W.prototype.destroy = W.prototype.o;
        W.version = "2.0.0-beta3-npm-dirty";
        var Ie = {};
        W.registerSupportPlugin = function (a, b) {
            Ie[a] = b
        };
        W.isBrowserSupported = function () {
            return !!window.Promise && !!window.Uint8Array && !!Array.prototype.forEach && !!window.MediaSource && !!window.MediaKeys && !!window.navigator && !!window.navigator.requestMediaKeySystemAccess && !!window.MediaKeySystemAccess && !!window.MediaKeySystemAccess.prototype.getConfiguration
        };
        W.probeSupport = function () {
            return hd().then(function (a) {
                var b = oc(), c = kd();
                a = {manifest: b, media: c, drm: a};
                for (var d in Ie) a[d] = Ie[d]();
                return a
            })
        };
        W.prototype.load = function (a, b, c) {
            var d = this.$a(), e = new ve;
            this.j = e;
            this.dispatchEvent(new q("loading"));
            return we(e.then(function () {
                return d
            }).then(function () {
                return pc(a, this.h, this.a.manifest.retryParameters, c)
            }.bind(this)).then(function (b) {
                this.l = new b;
                this.l.configure(this.a.manifest);
                return this.l.start(a, this.h, this.Ja.bind(this), this.$.bind(this))
            }.bind(this)).then(function (b) {
                this.c = b;
                this.la = a;
                this.g = new Tc(this.h, this.$.bind(this), this.Bc.bind(this));
                this.g.configure(this.a.drm);
                return this.g.init(b,
                    !1)
            }.bind(this)).then(function () {
                this.c.periods.forEach(this.Ja.bind(this));
                this.ja = Date.now() / 1E3;
                return Promise.all([Xc(this.g, this.f), this.ma])
            }.bind(this)).then(function () {
                this.i = new zd(this.f, this.c.presentationTimeline, 1 * Math.max(this.c.minBufferTime || 0, this.a.streaming.rebufferingGoal), b || null, this.zb.bind(this), this.Cc.bind(this));
                this.G = new jd(this.f, this.B, this.m);
                this.b = new Hd(this.i, this.G, this.h, this.c, this.Ac.bind(this), this.Cb.bind(this), this.$.bind(this));
                this.b.configure(this.a.streaming);
                return this.b.init()
            }.bind(this)).then(function () {
                this.c.periods.forEach(this.Ja.bind(this));
                Je(this);
                Ke(this);
                this.a.abr.manager.init(this.Ya.bind(this));
                this.j = null
            }.bind(this)))["catch"](function (a) {
                this.j == e && (this.j = null, this.dispatchEvent(new q("unloading")));
                return Promise.reject(a)
            }.bind(this))
        };
        W.prototype.load = W.prototype.load;

        function Ge(a) {
            a.B = new MediaSource;
            var b = new v;
            x(a.v, a.B, "sourceopen", b.resolve);
            a.f.src = window.URL.createObjectURL(a.B);
            return b
        }

        W.prototype.configure = function (a) {
            a.abr && a.abr.manager && a.abr.manager != this.a.abr.manager && (this.a.abr.manager.stop(), a.abr.manager.init(this.Ya.bind(this)));
            ye(this.a, a, Fe(this), Le(), "");
            Me(this)
        };
        W.prototype.configure = W.prototype.configure;

        function Me(a) {
            a.l && a.l.configure(a.a.manifest);
            a.g && a.g.configure(a.a.drm);
            a.b && (a.b.configure(a.a.streaming), a.c.periods.forEach(a.Ja.bind(a)), Ne(a, Ld(a.b)));
            a.a.abr.enabled && !a.oa ? a.a.abr.manager.enable() : a.a.abr.manager.disable();
            a.a.abr.manager.setDefaultEstimate(a.a.abr.defaultBandwidthEstimate)
        }

        W.prototype.getConfiguration = function () {
            var a = Fe(this);
            ye(a, this.a, Fe(this), Le(), "");
            return a
        };
        W.prototype.getConfiguration = W.prototype.getConfiguration;
        W.prototype.sc = function () {
            var a = Fe(this);
            a.abr && a.abr.manager && a.abr.manager != this.a.abr.manager && (this.a.abr.manager.stop(), a.abr.manager.init(this.Ya.bind(this)));
            this.a = Fe(this);
            Me(this)
        };
        W.prototype.resetConfiguration = W.prototype.sc;
        W.prototype.gb = function () {
            return this.h
        };
        W.prototype.getNetworkingEngine = W.prototype.gb;
        W.prototype.Ib = function () {
            return this.la
        };
        W.prototype.getManifestUri = W.prototype.Ib;
        W.prototype.T = function () {
            return this.c ? this.c.presentationTimeline.T() : !1
        };
        W.prototype.isLive = W.prototype.T;
        W.prototype.vc = function () {
            var a = 0, b = 0;
            this.c && (b = this.c.presentationTimeline, a = b.qa(), b = b.hb());
            return {start: a, end: b}
        };
        W.prototype.seekRange = W.prototype.vc;
        W.prototype.keySystem = function () {
            return this.g ? this.g.keySystem() : ""
        };
        W.prototype.keySystem = W.prototype.keySystem;
        W.prototype.drmInfo = function () {
            return this.g ? this.g.b : null
        };
        W.prototype.drmInfo = W.prototype.drmInfo;
        W.prototype.Mb = function () {
            return this.Ma
        };
        W.prototype.isBuffering = W.prototype.Mb;
        W.prototype.$a = function () {
            if (this.w) return Promise.resolve();
            this.dispatchEvent(new q("unloading"));
            if (this.j) {
                var a = new t(7, 7E3);
                return xe(this.j, a).then(this.tb.bind(this))
            }
            return this.tb()
        };
        W.prototype.unload = W.prototype.$a;
        W.prototype.Oa = function () {
            return this.i ? this.i.Oa() : 0
        };
        W.prototype.getPlaybackRate = W.prototype.Oa;
        W.prototype.Nc = function (a) {
            this.i && Ed(this.i, a)
        };
        W.prototype.trickPlay = W.prototype.Nc;
        W.prototype.Db = function () {
            this.i && Ed(this.i, 1)
        };
        W.prototype.cancelTrickPlay = W.prototype.Db;
        W.prototype.getTracks = function () {
            if (!this.b) return [];
            var a = Md(this.b);
            return Be(Ld(this.b), a).filter(function (a) {
                return 0 > this.ka.indexOf(a.id)
            }.bind(this))
        };
        W.prototype.getTracks = W.prototype.getTracks;
        W.prototype.wc = function (a, b) {
            if (this.b) {
                var c = Ce(Ld(this.b), a);
                if (c && (c = c.stream, c.allowedByApplication && c.allowedByKeySystem)) {
                    this.F.push({timestamp: Date.now() / 1E3, id: c.id, type: a.type, fromAdaptation: !1});
                    "text" != a.type && this.configure({abr: {enabled: !1}});
                    var d = {};
                    d[a.type] = c;
                    Oe(this, d, b)
                }
            }
        };
        W.prototype.selectTrack = W.prototype.wc;
        W.prototype.Pb = function () {
            return "showing" == this.m.mode
        };
        W.prototype.isTextTrackVisible = W.prototype.Pb;
        W.prototype.yc = function (a) {
            this.m.mode = a ? "showing" : "hidden";
            Pe(this)
        };
        W.prototype.setTextTrackVisibility = W.prototype.yc;
        W.prototype.getStats = function () {
            Qe(this);
            var a = {}, b = {}, c = this.f && this.f.getVideoPlaybackQuality ? this.f.getVideoPlaybackQuality() : {};
            this.b && (b = Md(this.b), a = b.video || {}, b = b.audio || {});
            return {
                width: a.width || 0,
                height: a.height || 0,
                streamBandwidth: a.bandwidth + b.bandwidth || 0,
                decodedFrames: Number(c.totalVideoFrames),
                droppedFrames: Number(c.droppedVideoFrames),
                estimatedBandwidth: this.a.abr.manager.getBandwidthEstimate(),
                playTime: this.na,
                bufferingTime: this.L,
                switchHistory: this.F.slice(0)
            }
        };
        W.prototype.getStats = W.prototype.getStats;
        W.prototype.addTextTrack = function (a, b, c, d, e) {
            if (!this.b) return Promise.reject();
            for (var f = Ld(this.b), g, k = 0; k < this.c.periods.length; k++) if (this.c.periods[k] == f) {
                if (k == this.c.periods.length - 1) {
                    if (g = this.c.presentationTimeline.X() - f.startTime, g == Number.POSITIVE_INFINITY) return Promise.reject()
                } else g = this.c.periods[k + 1].startTime - f.startTime;
                break
            }
            var l = {
                id: this.Ab++,
                createSegmentIndex: Promise.resolve.bind(Promise),
                findSegmentPosition: function () {
                    return 1
                },
                getSegmentReference: function (b) {
                    return 1 != b ?
                        null : new H(1, 0, g, function () {
                            return [a]
                        }, 0, null)
                },
                initSegmentReference: null,
                presentationTimeOffset: 0,
                mimeType: d,
                codecs: e || "",
                bandwidth: 0,
                kind: c,
                encrypted: !1,
                keyId: null,
                language: b,
                allowedByApplication: !0,
                allowedByKeySystem: !0
            };
            d = {language: b, type: "text", primary: !1, drmInfos: [], streams: [l]};
            this.ka.push(l.id);
            f.streamSets.push(d);
            return Nd(this.b, l).then(function () {
                if (!this.w) return this.ka.splice(this.ka.indexOf(l.id), 1), Ne(this, f), Je(this), {
                    id: l.id, active: !1, type: "text", bandwidth: 0, language: b, kind: c,
                    width: null, height: null
                }
            }.bind(this))
        };
        W.prototype.addTextTrack = W.prototype.addTextTrack;
        W.prototype.xb = function (a, b) {
            this.za.width = a;
            this.za.height = b
        };
        W.prototype.setMaxHardwareResolution = W.prototype.xb;

        function He(a) {
            a.v && a.v.ha(a.B, "sourceopen");
            a.f && (a.f.removeAttribute("src"), a.f.load());
            var b = Promise.all([a.a ? a.a.abr.manager.stop() : null, a.g ? a.g.o() : null, a.G ? a.G.o() : null, a.i ? a.i.o() : null, a.b ? a.b.o() : null, a.l ? a.l.stop() : null]);
            a.g = null;
            a.G = null;
            a.i = null;
            a.b = null;
            a.l = null;
            a.c = null;
            a.la = null;
            a.ma = null;
            a.B = null;
            a.u = {};
            a.F = [];
            a.na = 0;
            a.L = 0;
            return b
        }

        h = W.prototype;
        h.tb = function () {
            return this.l ? He(this).then(function () {
                this.w || (this.zb(!1), this.ma = Ge(this))
            }.bind(this)) : Promise.resolve()
        };

        function Le() {
            return {
                ".drm.servers": "",
                ".drm.clearKeys": "",
                ".drm.advanced": {
                    distinctiveIdentifierRequired: !1,
                    persistentStateRequired: !1,
                    videoRobustness: "",
                    audioRobustness: "",
                    serverCertificate: null
                }
            }
        }

        function Fe(a) {
            return {
                drm: {retryParameters: kc(), servers: {}, clearKeys: {}, advanced: {}},
                manifest: {retryParameters: kc(), dash: {customScheme: new Function("node", ""), clockSyncUri: ""}},
                streaming: {retryParameters: kc(), rebufferingGoal: 2, bufferingGoal: 30, bufferBehind: 30},
                abr: {manager: a.ab, enabled: !0, defaultBandwidthEstimate: 5E5},
                preferredAudioLanguage: "",
                preferredTextLanguage: "",
                restrictions: {
                    minWidth: 0,
                    maxWidth: Number.POSITIVE_INFINITY,
                    minHeight: 0,
                    maxHeight: Number.POSITIVE_INFINITY,
                    minPixels: 0,
                    maxPixels: Number.POSITIVE_INFINITY,
                    minAudioBandwidth: 0,
                    maxAudioBandwidth: Number.POSITIVE_INFINITY,
                    minVideoBandwidth: 0,
                    maxVideoBandwidth: Number.POSITIVE_INFINITY
                }
            }
        }

        h.Ja = function (a) {
            var b = this.b ? Md(this.b) : {};
            Ae(this.g, b, a);
            b = a.streamSets.some(De);
            ze(a, this.a.restrictions, this.za) && !this.j && Je(this);
            a = !a.streamSets.some(De);
            b ? a && this.$(new t(4, 4012)) : this.$(new t(4, 4011))
        };

        function Oe(a, b, c) {
            for (var d in b) {
                var e = b[d], f = c || "text" == d;
                a.oa ? a.u[d] = {stream: e, clear: f} : Od(a.b, d, e, f ? 0 : void 0)
            }
        }

        function Qe(a) {
            if (a.c) {
                var b = Date.now() / 1E3;
                a.Ma ? a.L += b - a.ja : a.na += b - a.ja;
                a.ja = b
            }
        }

        h.Dc = function (a, b, c) {
            this.a.abr.manager.segmentDownloaded(a, b, c)
        };
        h.zb = function (a) {
            Qe(this);
            this.Ma = a;
            this.dispatchEvent(new q("buffering", {buffering: a}))
        };
        h.Cc = function () {
            if (this.b) {
                var a = this.b, b;
                for (b in a.b) {
                    var c = a.b[b];
                    c.ca || 0 < nd(a.c, b, Ad(a.h)) || c.ba || (c.ea ? Qd(c, 0) : null == ld(a.c, b) ? null == c.aa && Ud(a, c, 0) : (Id(c), Rd(a, c, 0)))
                }
            }
        };

        function Re(a, b, c) {
            if (!C(b).some(De)) return a.$(new t(4, 4012)), {};
            var d = {};
            if (c) d = b; else {
                c = Md(a.b);
                for (var e in c) {
                    var f = c[e];
                    f.allowedByApplication && f.allowedByKeySystem && b[e].language == f.language || (d[e] = b[e])
                }
            }
            if (Pa(d)) return {};
            ia(Object.keys(d));
            var g = a.a.abr.manager.chooseStreams(d);
            return Ra(d, function (a) {
                return !!g[a]
            }) ? g : (a.$(new t(4, 4012)), {})
        }

        function Ne(a, b) {
            var c = {audio: !1, text: !1}, d = Ee(b, a.a, c), e = Re(a, d), f;
            for (f in e) a.F.push({timestamp: Date.now() / 1E3, id: e[f].id, type: f, fromAdaptation: !0});
            Oe(a, e, !0);
            Ke(a);
            d.text && d.audio && c.text && d.text.language != d.audio.language && (a.m.mode = "showing", Pe(a))
        }

        h.Ac = function (a) {
            this.oa = !0;
            this.a.abr.manager.disable();
            a = Ee(a, this.a);
            a = Re(this, a, !0);
            for (var b in this.u) a[b] = this.u[b].stream;
            this.u = {};
            for (b in a) this.F.push({timestamp: Date.now() / 1E3, id: a[b].id, type: b, fromAdaptation: !0});
            this.j || Je(this);
            return a
        };
        h.Cb = function () {
            this.oa = !1;
            this.a.abr.enabled && this.a.abr.manager.enable();
            for (var a in this.u) {
                var b = this.u[a];
                Od(this.b, a, b.stream, b.clear ? 0 : void 0)
            }
            this.u = {}
        };
        h.Ya = function (a, b) {
            var c = Md(this.b), d;
            for (d in a) {
                var e = a[d];
                c[d] != e ? this.F.push({
                    timestamp: Date.now() / 1E3,
                    id: e.id,
                    type: d,
                    fromAdaptation: !0
                }) : delete a[d]
            }
            if (!Pa(a) && this.b) {
                for (d in a) Od(this.b, d, a[d], "video" == d ? b : void 0);
                Ke(this)
            }
        };

        function Ke(a) {
            Promise.resolve().then(function () {
                this.w || this.dispatchEvent(new q("adaptation"))
            }.bind(a))
        }

        function Je(a) {
            Promise.resolve().then(function () {
                this.w || this.dispatchEvent(new q("trackschanged"))
            }.bind(a))
        }

        function Pe(a) {
            a.dispatchEvent(new q("texttrackvisibility"))
        }

        h.$ = function (a) {
            this.dispatchEvent(new q("error", {detail: a}))
        };
        h.dc = function () {
            if (this.f.error) {
                var a = this.f.error.code;
                if (1 != a) {
                    var b = this.f.error.msExtendedCode;
                    b && (0 > b && (b += Math.pow(2, 32)), b = b.toString(16));
                    this.$(new t(3, 3016, a, b))
                }
            }
        };
        h.Bc = function (a) {
            var b = ["usable", "status-pending", "output-downscaled", "expired"], c = Ld(this.b), d = !1;
            c.streamSets.forEach(function (c) {
                c.streams.forEach(function (c) {
                    var e = c.allowedByKeySystem;
                    c.keyId && c.keyId in a && (c.allowedByKeySystem = 0 <= b.indexOf(a[c.keyId]));
                    e != c.allowedByKeySystem && (d = !0)
                })
            });
            Ne(this, c);
            d && Je(this)
        };

        function X(a) {
            this.a = new he;
            this.c = a;
            this.j = Se(this);
            this.g = null;
            this.v = !1;
            this.i = null;
            this.l = [];
            this.f = -1;
            this.m = 0;
            this.b = null;
            this.h = new pe(a.h, a.getConfiguration().streaming.retryParameters, this.j)
        }

        m("shaka.offline.Storage", X);

        function Te() {
            return !!window.indexedDB
        }

        X.support = Te;
        X.prototype.o = function () {
            var a = this.l, b = this.a, c = this.h ? this.h.o()["catch"](function () {
            }).then(function () {
                return Promise.all(a.map(function (a) {
                    return b.remove("segment", a)
                }))
            }).then(function () {
                return b.o()
            }) : Promise.resolve();
            this.j = this.c = this.h = this.a = null;
            return c
        };
        X.prototype.destroy = X.prototype.o;
        X.prototype.configure = function (a) {
            ye(this.j, a, Se(this), {}, "")
        };
        X.prototype.configure = X.prototype.configure;
        X.prototype.Kc = function (a, b, c) {
            function d(a) {
                f = a
            }

            if (this.v) return Promise.reject(new t(9, 9006));
            this.v = !0;
            var e, f = null;
            return Ue(this).then(function () {
                Y(this);
                return Ve(this, a, d, c)
            }.bind(this)).then(function (c) {
                Y(this);
                this.b = c.manifest;
                this.g = c.Gb;
                if (this.b.presentationTimeline.T()) throw new t(9, 9005, a);
                this.b.periods.forEach(this.u.bind(this));
                this.f = this.a.b.manifest++;
                this.m = 0;
                c = this.b.periods.map(this.w.bind(this));
                var d = this.g.b, f = bd(this.g);
                if (d) {
                    if (!f.length) throw new t(9, 9007, a);
                    d.initData =
                        []
                }
                e = {
                    key: this.f,
                    originalManifestUri: a,
                    duration: this.m,
                    size: 0,
                    periods: c,
                    sessionIds: f,
                    drmInfo: d,
                    appMetadata: b
                };
                return re(this.h, e)
            }.bind(this)).then(function () {
                Y(this);
                if (f) throw f;
                return le(this.a, "manifest", e)
            }.bind(this)).then(function () {
                return We(this)
            }.bind(this)).then(function () {
                return oe(e)
            }.bind(this))["catch"](function (a) {
                return We(this)["catch"](B).then(function () {
                    throw a;
                })
            }.bind(this))
        };
        X.prototype.store = X.prototype.Kc;
        X.prototype.remove = function (a) {
            function b(a) {
                6013 != a.code && (e = a)
            }

            var c = a.offlineUri, d = /^offline:([0-9]+)$/.exec(c);
            if (!d) return Promise.reject(new t(9, 9004, c));
            var e = null, f, g, k = Number(d[1]);
            return Ue(this).then(function () {
                Y(this);
                return this.a.get("manifest", k)
            }.bind(this)).then(function (a) {
                Y(this);
                if (!a) throw new t(9, 9003, c);
                f = a;
                a = ue(f);
                g = new Tc(this.c.h, b, function () {
                });
                g.configure(this.c.getConfiguration().drm);
                return g.init(a, !0)
            }.bind(this)).then(function () {
                return Zc(g, f.sessionIds)
            }.bind(this)).then(function () {
                return g.o()
            }.bind(this)).then(function () {
                Y(this);
                if (e) throw e;
                var b = f.periods.map(function (a) {
                    return a.streams.map(function (a) {
                        var b = a.segments.map(function (a) {
                            return Number(/^offline:[0-9]+\/[0-9]+\/([0-9]+)$/.exec(a.uri)[1])
                        });
                        a.initSegmentUri && b.push(Number(/^offline:[0-9]+\/[0-9]+\/([0-9]+)$/.exec(a.initSegmentUri)[1]));
                        return b
                    }).reduce(A, [])
                }).reduce(A, []), c = 0, d = b.length, g = this.j.progressCallback;
                return me(this.a, function (e) {
                    e = b.indexOf(e.key);
                    0 <= e && (g(a, c / d), c++);
                    return 0 <= e
                }.bind(this))
            }.bind(this)).then(function () {
                Y(this);
                this.j.progressCallback(a,
                    1);
                return this.a.remove("manifest", k)
            }.bind(this))
        };
        X.prototype.remove = X.prototype.remove;
        X.prototype.list = function () {
            var a = [];
            return Ue(this).then(function () {
                Y(this);
                return this.a.forEach("manifest", function (b) {
                    a.push(oe(b))
                })
            }.bind(this)).then(function () {
                return a
            })
        };
        X.prototype.list = X.prototype.list;

        function Ve(a, b, c, d) {
            function e() {
            }

            var f = a.c.h, g = a.c.getConfiguration(), k, l, p;
            return pc(b, f, g.manifest.retryParameters, d).then(function (a) {
                Y(this);
                p = new a;
                p.configure(g.manifest);
                return p.start(b, f, this.u.bind(this), c)
            }.bind(a)).then(function (a) {
                Y(this);
                k = a;
                l = new Tc(f, c, e);
                l.configure(g.drm);
                return l.init(k, !0)
            }.bind(a)).then(function () {
                Y(this);
                return Xe(k)
            }.bind(a)).then(function () {
                Y(this);
                return Yc(l)
            }.bind(a)).then(function () {
                Y(this);
                return p.stop()
            }.bind(a)).then(function () {
                Y(this);
                return {
                    manifest: k,
                    Gb: l
                }
            }.bind(a))["catch"](function (a) {
                if (p) return p.stop().then(function () {
                    throw a;
                });
                throw a;
            })
        }

        X.prototype.B = function (a) {
            var b = [], c = a.filter(function (a) {
                return "video" == a.type && 480 >= a.height
            });
            c.sort(function (a, b) {
                return b.bandwidth - a.bandwidth
            });
            c.length && b.push(c[0]);
            for (var d = xc(this.c.getConfiguration().preferredAudioLanguage), c = [0, vc, wc],
                     e = a.filter(function (a) {
                         return "audio" == a.type
                     }), c = c.map(function (a) {
                    return e.filter(function (b) {
                        b = xc(b.language);
                        return uc(a, d, b)
                    })
                }), f = e, g = 0; g < c.length; g++) c[g].length && (f = c[g]);
            f.sort(function (a, b) {
                return a.bandwidth - b.bandwidth
            });
            f.length && b.push(f[Math.floor(f.length /
                2)]);
            var c = xc(this.c.getConfiguration().preferredTextLanguage), k = uc.bind(null, wc, c);
            b.push.apply(b, a.filter(function (a) {
                var b = xc(a.language);
                return "text" == a.type && k(b)
            }));
            return b
        };

        function Se(a) {
            return {trackSelectionCallback: a.B.bind(a), progressCallback: new Function("storedContent", "percent", "")}
        }

        function Ue(a) {
            return a.a.a ? Promise.resolve() : a.a.init(ne)
        }

        X.prototype.u = function (a) {
            function b(a, b, c) {
                b = b.filter(function (a) {
                    return a.type == c
                });
                return 0 == b.length ? null : Ce(a, b[0]).stream
            }

            var c = {};
            this.i && (c = {
                video: b(this.b.periods[0], this.i, "video"),
                audio: b(this.b.periods[0], this.i, "audio")
            });
            Ae(this.g, c, a);
            ze(a, this.c.getConfiguration().restrictions, {width: Infinity, height: Infinity})
        };

        function We(a) {
            var b = a.g ? a.g.o() : Promise.resolve();
            a.g = null;
            a.b = null;
            a.v = !1;
            a.i = null;
            a.l = [];
            a.f = -1;
            return b
        }

        function Xe(a) {
            a = a.periods.map(function (a) {
                return a.streamSets
            }).reduce(A, []).map(function (a) {
                return a.streams
            }).reduce(A, []);
            return Promise.all(a.map(function (a) {
                return a.createSegmentIndex()
            }))
        }

        X.prototype.w = function (a) {
            var b = Be(a, null), b = this.j.trackSelectionCallback(b);
            this.i || (this.i = b, this.b.periods.forEach(this.u.bind(this)));
            for (var c = b.length - 1; 0 < c; --c) {
                for (var d = !1,
                         e = c - 1; 0 <= e; --e) if (b[c].type == b[e].type && b[c].kind == b[e].kind && b[c].language == b[e].language) {
                    d = !0;
                    break
                }
                if (d) break
            }
            b = b.map(function (b) {
                b = Ce(a, b);
                return Ye(this, a, b.Lc, b.stream)
            }.bind(this));
            return {startTime: a.startTime, streams: b}
        };

        function Ye(a, b, c, d) {
            for (var e = [], f = a.b.presentationTimeline.pa(), g = f, k = d.findSegmentPosition(f),
                     l = null != k ? d.getSegmentReference(k) : null; l;) {
                var p = a.a.b.segment++;
                qe(a.h, c.type, l, (l.endTime - l.startTime) * d.bandwidth / 8, function (a, b, c, d) {
                    b = {key: a, data: d, manifestKey: this.f, streamNumber: c, segmentNumber: b};
                    this.l.push(a);
                    return le(this.a, "segment", b)
                }.bind(a, p, l.position, d.id));
                e.push({startTime: l.startTime, endTime: l.endTime, uri: "offline:" + a.f + "/" + d.id + "/" + p});
                g = l.endTime + b.startTime;
                l = d.getSegmentReference(++k)
            }
            a.m =
                Math.max(a.m, g - f);
            b = null;
            d.initSegmentReference && (p = a.a.b.segment++, b = "offline:" + a.f + "/" + d.id + "/" + p, qe(a.h, c.type, d.initSegmentReference, 0, function (a, b) {
                var c = {key: p, data: b, manifestKey: this.f, streamNumber: a, segmentNumber: -1};
                this.l.push(p);
                return le(this.a, "segment", c)
            }.bind(a, d.id)));
            return {
                id: d.id,
                primary: c.primary,
                presentationTimeOffset: d.presentationTimeOffset || 0,
                contentType: c.type,
                mimeType: d.mimeType,
                codecs: d.codecs,
                kind: d.kind,
                language: c.language,
                width: d.width || null,
                height: d.height || null,
                initSegmentUri: b,
                encrypted: d.encrypted,
                keyId: d.keyId,
                segments: e
            }
        }

        function Y(a) {
            if (!a.c) throw new t(9, 9002);
        }

        Ie.offline = Te;
        m("shaka.polyfill.installAll", function () {
            for (var a = 0; a < Ze.length; ++a) Ze[a]()
        });
        var Ze = [];

        function $e(a) {
            Ze.push(a)
        }

        m("shaka.polyfill.register", $e);

        function af(a) {
            var b = a.type.replace(/^(webkit|moz|MS)/, "").toLowerCase(), b = new Event(b, a);
            a.target.dispatchEvent(b)
        }

        $e(function () {
            if (window.Document) {
                var a = Element.prototype;
                a.requestFullscreen = a.requestFullscreen || a.mozRequestFullScreen || a.msRequestFullscreen || a.webkitRequestFullscreen;
                a = Document.prototype;
                a.exitFullscreen = a.exitFullscreen || a.mozCancelFullScreen || a.msExitFullscreen || a.webkitExitFullscreen;
                "fullscreenElement" in document || Object.defineProperty(document, "fullscreenElement", {
                    get: function () {
                        return document.mozFullScreenElement || document.msFullscreenElement || document.webkitFullscreenElement
                    }
                });
                document.addEventListener("webkitfullscreenchange",
                    af);
                document.addEventListener("webkitfullscreenerror", af);
                document.addEventListener("mozfullscreenchange", af);
                document.addEventListener("mozfullscreenerror", af);
                document.addEventListener("MSFullscreenChange", af);
                document.addEventListener("MSFullscreenError", af)
            }
        });

        function bf(a) {
            this.c = [];
            this.b = [];
            this.a = [];
            for (a = new Fb(new DataView(a.buffer)); Hb(a);) {
                var b = Nb(1886614376, a);
                if (-1 == b) break;
                var c = a.a - 8, d = Ib(a);
                if (1 < d) M(a, b - (a.a - c)); else {
                    M(a, 3);
                    var e = Va(Mb(a, 16)), f = [];
                    if (0 < d) for (var d = L(a), g = 0; g < d; ++g) {
                        var k = Va(Mb(a, 16));
                        f.push(k)
                    }
                    d = L(a);
                    M(a, d);
                    this.b.push.apply(this.b, f);
                    this.c.push(e);
                    this.a.push({start: c, end: a.a - 1});
                    a.a != c + b && M(a, b - (a.a - c))
                }
            }
        };

        function cf(a, b) {
            try {
                var c = new df(a, b);
                return Promise.resolve(c)
            } catch (d) {
                return Promise.reject(d)
            }
        }

        function df(a, b) {
            this.keySystem = a;
            for (var c = !1, d = 0; d < b.length; ++d) {
                var e = b[d], f = {
                    audioCapabilities: [],
                    videoCapabilities: [],
                    persistentState: "optional",
                    distinctiveIdentifier: "optional",
                    initDataTypes: e.initDataTypes,
                    sessionTypes: ["temporary"],
                    label: e.label
                }, g = !1;
                if (e.audioCapabilities) for (var k = 0; k < e.audioCapabilities.length; ++k) {
                    var l = e.audioCapabilities[k];
                    if (l.contentType) {
                        var g = !0, p = l.contentType.split(";")[0];
                        MSMediaKeys.isTypeSupported(this.keySystem, p) && (f.audioCapabilities.push(l), c = !0)
                    }
                }
                if (e.videoCapabilities) for (k =
                                                  0; k < e.videoCapabilities.length; ++k) l = e.videoCapabilities[k], l.contentType && (g = !0, p = l.contentType.split(";")[0], MSMediaKeys.isTypeSupported(this.keySystem, p) && (f.videoCapabilities.push(l), c = !0));
                g || (c = MSMediaKeys.isTypeSupported(this.keySystem, "video/mp4"));
                "required" == e.persistentState && (f.persistentState = "required", f.sessionTypes = ["persistent-license"]);
                if (c) {
                    this.a = f;
                    return
                }
            }
            c = Error("Unsupported keySystem");
            c.name = "NotSupportedError";
            c.code = DOMException.NOT_SUPPORTED_ERR;
            throw c;
        }

        df.prototype.createMediaKeys = function () {
            var a = new ef(this.keySystem);
            return Promise.resolve(a)
        };
        df.prototype.getConfiguration = function () {
            return this.a
        };

        function ff(a) {
            var b = this.mediaKeys;
            b && b != a && gf(b, null);
            delete this.mediaKeys;
            return (this.mediaKeys = a) ? gf(a, this) : Promise.resolve()
        }

        function ef(a) {
            this.a = new MSMediaKeys(a);
            this.b = new w
        }

        ef.prototype.createSession = function (a) {
            if ("temporary" != (a || "temporary")) throw new TypeError("Session type " + a + " is unsupported on this platform.");
            return new hf(this.a)
        };
        ef.prototype.setServerCertificate = function () {
            return Promise.reject(Error("setServerCertificate not supported on this platform."))
        };

        function gf(a, b) {
            function c() {
                b.msSetMediaKeys(d.a);
                b.removeEventListener("loadedmetadata", c)
            }

            Fa(a.b);
            if (!b) return Promise.resolve();
            x(a.b, b, "msneedkey", jf);
            var d = a;
            try {
                return 1 <= b.readyState ? b.msSetMediaKeys(a.a) : b.addEventListener("loadedmetadata", c), Promise.resolve()
            } catch (e) {
                return Promise.reject(e)
            }
        }

        function hf(a) {
            n.call(this);
            this.c = null;
            this.g = a;
            this.b = this.a = null;
            this.f = new w;
            this.sessionId = "";
            this.expiration = NaN;
            this.closed = new v;
            this.keyStatuses = new kf
        }

        ba(hf);
        h = hf.prototype;
        h.generateRequest = function (a, b) {
            this.a = new v;
            try {
                this.c = this.g.createSession("video/mp4", new Uint8Array(b), null), x(this.f, this.c, "mskeymessage", this.Zb.bind(this)), x(this.f, this.c, "mskeyadded", this.Xb.bind(this)), x(this.f, this.c, "mskeyerror", this.Yb.bind(this)), lf(this, "status-pending")
            } catch (c) {
                this.a.reject(c)
            }
            return this.a
        };
        h.load = function () {
            return Promise.reject(Error("MediaKeySession.load not yet supported"))
        };
        h.update = function (a) {
            this.b = new v;
            try {
                this.c.update(new Uint8Array(a))
            } catch (b) {
                this.b.reject(b)
            }
            return this.b
        };
        h.close = function () {
            try {
                this.c.close(), this.closed.resolve(), Fa(this.f)
            } catch (a) {
                this.closed.reject(a)
            }
            return this.closed
        };
        h.remove = function () {
            return Promise.reject(Error("MediaKeySession.remove is only applicable for persistent licenses, which are not supported on this platform"))
        };

        function jf(a) {
            var b = document.createEvent("CustomEvent");
            b.initCustomEvent("encrypted", !1, !1, null);
            b.initDataType = "cenc";
            if (a = a.initData) {
                var c = new bf(a);
                if (!(1 >= c.a.length)) {
                    for (var d = [], e = 0; e < c.a.length; e++) d.push(a.subarray(c.a[e].start, c.a[e].end + 1));
                    e = mf;
                    a = [];
                    for (c = 0; c < d.length; ++c) {
                        for (var f = !1, g = 0; g < a.length && !(f = e ? e(d[c], a[g]) : d[c] === a[g]); ++g) ;
                        f || a.push(d[c])
                    }
                    for (e = d = 0; e < a.length; e++) d += a[e].length;
                    d = new Uint8Array(d);
                    for (e = c = 0; e < a.length; e++) d.set(a[e], c), c += a[e].length;
                    a = d
                }
            }
            b.initData =
                a;
            this.dispatchEvent(b)
        }

        function mf(a, b) {
            return Wa(a, b)
        }

        h.Zb = function (a) {
            this.a && (this.a.resolve(), this.a = null);
            this.dispatchEvent(new q("message", {
                messageType: void 0 == this.keyStatuses.Pa() ? "licenserequest" : "licenserenewal",
                message: a.message.buffer
            }))
        };
        h.Xb = function () {
            this.a ? (lf(this, "usable"), this.a.resolve(), this.a = null) : this.b && (lf(this, "usable"), this.b.resolve(), this.b = null)
        };
        h.Yb = function () {
            var a = Error("EME PatchedMediaKeysMs key error");
            a.errorCode = this.c.error;
            if (this.a) this.a.reject(a), this.a = null; else if (this.b) this.b.reject(a), this.b = null; else switch (this.c.error.code) {
                case MSMediaKeyError.MS_MEDIA_KEYERR_OUTPUT:
                case MSMediaKeyError.MS_MEDIA_KEYERR_HARDWARECHANGE:
                    lf(this, "output-not-allowed");
                default:
                    lf(this, "internal-error")
            }
        };

        function lf(a, b) {
            a.keyStatuses.Xa(b);
            a.dispatchEvent(new q("keystatuseschange"))
        }

        function kf() {
            this.size = 0;
            this.a = void 0
        }

        var nf;
        h = kf.prototype;
        h.Xa = function (a) {
            this.size = void 0 == a ? 0 : 1;
            this.a = a
        };
        h.Pa = function () {
            return this.a
        };
        h.forEach = function (a) {
            this.a && a(this.a, nf)
        };
        h.get = function (a) {
            if (this.has(a)) return this.a
        };
        h.has = function (a) {
            var b = nf;
            return this.a && Wa(new Uint8Array(a), new Uint8Array(b)) ? !0 : !1
        };

        function of() {
            return Promise.reject(Error("The key system specified is not supported."))
        }

        function pf(a) {
            return a ? Promise.reject(Error("MediaKeys not supported.")) : Promise.resolve()
        }

        function qf() {
            throw new TypeError("Illegal constructor.");
        }

        qf.prototype.createSession = function () {
        };
        qf.prototype.setServerCertificate = function () {
        };

        function rf() {
            throw new TypeError("Illegal constructor.");
        }

        rf.prototype.getConfiguration = function () {
        };
        rf.prototype.createMediaKeys = function () {
        };

        function sf(a, b) {
            try {
                var c = new tf(a, b);
                return Promise.resolve(c)
            } catch (d) {
                return Promise.reject(d)
            }
        }

        function uf(a) {
            var b = this.mediaKeys;
            b && b != a && vf(b, null);
            delete this.mediaKeys;
            (this.mediaKeys = a) && vf(a, this);
            return Promise.resolve()
        }

        function tf(a, b) {
            this.a = this.keySystem = a;
            var c = !0;
            "org.w3.clearkey" == a && (this.a = "webkit-org.w3.clearkey", c = !1);
            var d = !1, e;
            e = document.getElementsByTagName("video");
            e = e.length ? e[0] : document.createElement("video");
            for (var f = 0; f < b.length; ++f) {
                var g = b[f], k = {
                    audioCapabilities: [],
                    videoCapabilities: [],
                    persistentState: "optional",
                    distinctiveIdentifier: "optional",
                    initDataTypes: g.initDataTypes,
                    sessionTypes: ["temporary"],
                    label: g.label
                }, l = !1;
                if (g.audioCapabilities) for (var p = 0; p < g.audioCapabilities.length; ++p) {
                    var r =
                        g.audioCapabilities[p];
                    r.contentType && (l = !0, e.canPlayType(r.contentType.split(";")[0], this.a) && (k.audioCapabilities.push(r), d = !0))
                }
                if (g.videoCapabilities) for (p = 0; p < g.videoCapabilities.length; ++p) r = g.videoCapabilities[p], r.contentType && (l = !0, e.canPlayType(r.contentType, this.a) && (k.videoCapabilities.push(r), d = !0));
                l || (d = e.canPlayType("video/mp4", this.a) || e.canPlayType("video/webm", this.a));
                "required" == g.persistentState && (c ? (k.persistentState = "required", k.sessionTypes = ["persistent-license"]) : d = !1);
                if (d) {
                    this.b =
                        k;
                    return
                }
            }
            c = "Unsupported keySystem";
            if ("org.w3.clearkey" == a || "com.widevine.alpha" == a) c = "None of the requested configurations were supported.";
            c = Error(c);
            c.name = "NotSupportedError";
            c.code = DOMException.NOT_SUPPORTED_ERR;
            throw c;
        }

        tf.prototype.createMediaKeys = function () {
            var a = new wf(this.a);
            return Promise.resolve(a)
        };
        tf.prototype.getConfiguration = function () {
            return this.b
        };

        function wf(a) {
            this.g = a;
            this.b = null;
            this.a = new w;
            this.c = [];
            this.f = {}
        }

        function vf(a, b) {
            a.b = b;
            Fa(a.a);
            b && (x(a.a, b, "webkitneedkey", a.hc.bind(a)), x(a.a, b, "webkitkeymessage", a.gc.bind(a)), x(a.a, b, "webkitkeyadded", a.ec.bind(a)), x(a.a, b, "webkitkeyerror", a.fc.bind(a)))
        }

        h = wf.prototype;
        h.createSession = function (a) {
            var b = a || "temporary";
            if ("temporary" != b && "persistent-license" != b) throw new TypeError("Session type " + a + " is unsupported on this platform.");
            a = this.b || document.createElement("video");
            a.src || (a.src = "about:blank");
            b = new xf(a, this.g, b);
            this.c.push(b);
            return b
        };
        h.setServerCertificate = function () {
            return Promise.reject(Error("setServerCertificate not supported on this platform."))
        };
        h.hc = function (a) {
            this.b.dispatchEvent(new q("encrypted", {initDataType: "webm", initData: a.initData}))
        };
        h.gc = function (a) {
            var b = yf(this, a.sessionId);
            b && (a = new q("message", {
                messageType: void 0 == b.keyStatuses.Pa() ? "licenserequest" : "licenserenewal",
                message: a.message
            }), b.b && (b.b.resolve(), b.b = null), b.dispatchEvent(a))
        };
        h.ec = function (a) {
            if (a = yf(this, a.sessionId)) zf(a, "usable"), a.a && a.a.resolve(), a.a = null
        };
        h.fc = function (a) {
            var b = yf(this, a.sessionId);
            if (b) {
                var c = Error("EME v0.1b key error");
                c.errorCode = a.errorCode;
                c.errorCode.systemCode = a.systemCode;
                !a.sessionId && b.b ? (c.method = "generateRequest", 45 == a.systemCode && (c.message = "Unsupported session type."), b.b.reject(c), b.b = null) : a.sessionId && b.a ? (c.method = "update", b.a.reject(c), b.a = null) : (c = a.systemCode, a.errorCode.code == MediaKeyError.MEDIA_KEYERR_OUTPUT ? zf(b, "output-restricted") : 1 == c ? zf(b, "expired") : zf(b, "internal-error"))
            }
        };

        function yf(a, b) {
            var c = a.f[b];
            return c ? c : (c = a.c.shift()) ? (c.sessionId = b, a.f[b] = c) : null
        }

        function xf(a, b, c) {
            n.call(this);
            this.f = a;
            this.h = !1;
            this.a = this.b = null;
            this.c = b;
            this.g = c;
            this.sessionId = "";
            this.expiration = NaN;
            this.closed = new v;
            this.keyStatuses = new Af
        }

        ba(xf);

        function Bf(a, b, c) {
            if (a.h) return Promise.reject(Error("The session is already initialized."));
            a.h = !0;
            var d;
            try {
                if ("persistent-license" == a.g) if (c) d = new Uint8Array(Dc("LOAD_SESSION|" + c)); else {
                    var e = Dc("PERSISTENT|"), f = new Uint8Array(e.byteLength + b.byteLength);
                    f.set(new Uint8Array(e), 0);
                    f.set(new Uint8Array(b), e.byteLength);
                    d = f
                } else d = new Uint8Array(b)
            } catch (g) {
                return Promise.reject(g)
            }
            a.b = new v;
            try {
                a.f.webkitGenerateKeyRequest(a.c, d)
            } catch (g) {
                if ("InvalidStateError" != g.name) return a.b = null, Promise.reject(g);
                setTimeout(function () {
                    try {
                        this.f.webkitGenerateKeyRequest(this.c, d)
                    } catch (a) {
                        this.b.reject(a), this.b = null
                    }
                }.bind(a), 10)
            }
            return a.b
        }

        h = xf.prototype;
        h.Za = function (a, b) {
            if (this.a) this.a.then(this.Za.bind(this, a, b))["catch"](this.Za.bind(this, a, b)); else {
                this.a = a;
                var c, d;
                "webkit-org.w3.clearkey" == this.c ? (c = zc(b), d = JSON.parse(c), "oct" != d.keys[0].kty && (this.a.reject(Error("Response is not a valid JSON Web Key Set.")), this.a = null), c = Ta(d.keys[0].k), d = Ta(d.keys[0].kid)) : (c = new Uint8Array(b), d = null);
                try {
                    this.f.webkitAddKey(this.c, c, d, this.sessionId)
                } catch (e) {
                    this.a.reject(e), this.a = null
                }
            }
        };

        function zf(a, b) {
            a.keyStatuses.Xa(b);
            a.dispatchEvent(new q("keystatuseschange"))
        }

        h.generateRequest = function (a, b) {
            return Bf(this, b, null)
        };
        h.load = function (a) {
            return "persistent-license" == this.g ? Bf(this, null, a) : Promise.reject(Error("Not a persistent session."))
        };
        h.update = function (a) {
            var b = new v;
            this.Za(b, a);
            return b
        };
        h.close = function () {
            if ("persistent-license" != this.g) {
                if (!this.sessionId) return this.closed.reject(Error("The session is not callable.")), this.closed;
                this.f.webkitCancelKeyRequest(this.c, this.sessionId)
            }
            this.closed.resolve();
            return this.closed
        };
        h.remove = function () {
            return "persistent-license" != this.g ? Promise.reject(Error("Not a persistent session.")) : this.close()
        };

        function Af() {
            this.size = 0;
            this.a = void 0
        }

        var Cf;
        h = Af.prototype;
        h.Xa = function (a) {
            this.size = void 0 == a ? 0 : 1;
            this.a = a
        };
        h.Pa = function () {
            return this.a
        };
        h.forEach = function (a) {
            this.a && a(this.a, Cf)
        };
        h.get = function (a) {
            if (this.has(a)) return this.a
        };
        h.has = function (a) {
            var b = Cf;
            return this.a && Wa(new Uint8Array(a), new Uint8Array(b)) ? !0 : !1
        };
        $e(function () {
            !window.HTMLVideoElement || navigator.requestMediaKeySystemAccess && MediaKeySystemAccess.prototype.getConfiguration || (HTMLMediaElement.prototype.webkitGenerateKeyRequest ? (Cf = (new Uint8Array([0])).buffer, navigator.requestMediaKeySystemAccess = sf, delete HTMLMediaElement.prototype.mediaKeys, HTMLMediaElement.prototype.mediaKeys = null, HTMLMediaElement.prototype.setMediaKeys = uf, window.MediaKeys = wf, window.MediaKeySystemAccess = tf) : window.MSMediaKeys ? (nf = (new Uint8Array([0])).buffer, delete HTMLMediaElement.prototype.mediaKeys,
                HTMLMediaElement.prototype.mediaKeys = null, HTMLMediaElement.prototype.setMediaKeys = ff, window.MediaKeys = ef, window.MediaKeySystemAccess = df, navigator.requestMediaKeySystemAccess = cf) : (navigator.requestMediaKeySystemAccess = of, delete HTMLMediaElement.prototype.mediaKeys, HTMLMediaElement.prototype.mediaKeys = null, HTMLMediaElement.prototype.setMediaKeys = pf, window.MediaKeys = qf, window.MediaKeySystemAccess = rf))
        });
        $e(function () {
            if (window.MediaSource) {
                var a = navigator.vendor, b = navigator.appVersion;
                if (a && b && !(0 > a.indexOf("Apple"))) if (0 <= b.indexOf("Version/8")) window.MediaSource = null; else {
                    var c = MediaSource.prototype.addSourceBuffer;
                    MediaSource.prototype.addSourceBuffer = function () {
                        var a = c.apply(this, arguments);
                        a.abort = function () {
                        };
                        return a
                    }
                }
            }
        });

        function Z(a) {
            this.c = [];
            this.b = [];
            this.ga = Df;
            if (a) try {
                a(this.V.bind(this), this.a.bind(this))
            } catch (b) {
                this.a(b)
            }
        }

        var Df = 0;

        function Ef(a) {
            var b = new Z;
            b.V(a);
            return b
        }

        function Ff(a) {
            var b = new Z;
            b.a(a);
            return b
        }

        function Gf(a) {
            function b(a, b, c) {
                a.ga == Df && (e[b] = c, d++, d == e.length && a.V(e))
            }

            var c = new Z;
            if (!a.length) return c.V([]), c;
            for (var d = 0, e = Array(a.length), f = c.a.bind(c),
                     g = 0; g < a.length; ++g) a[g] && a[g].then ? a[g].then(b.bind(null, c, g), f) : b(c, g, a[g]);
            return c
        }

        function Hf(a) {
            for (var b = new Z, c = b.V.bind(b), d = b.a.bind(b),
                     e = 0; e < a.length; ++e) a[e] && a[e].then ? a[e].then(c, d) : c(a[e]);
            return b
        }

        Z.prototype.then = function (a, b) {
            var c = new Z;
            switch (this.ga) {
                case 1:
                    If(this, c, a);
                    break;
                case 2:
                    If(this, c, b);
                    break;
                case Df:
                    this.c.push({H: c, Aa: a}), this.b.push({H: c, Aa: b})
            }
            return c
        };
        Z.prototype.then = Z.prototype.then;
        Z.prototype["catch"] = function (a) {
            return this.then(void 0, a)
        };
        Z.prototype["catch"] = Z.prototype["catch"];
        Z.prototype.V = function (a) {
            if (this.ga == Df) {
                this.La = a;
                this.ga = 1;
                for (a = 0; a < this.c.length; ++a) If(this, this.c[a].H, this.c[a].Aa);
                this.c = [];
                this.b = []
            }
        };
        Z.prototype.a = function (a) {
            if (this.ga == Df) {
                this.La = a;
                this.ga = 2;
                for (a = 0; a < this.b.length; ++a) If(this, this.b[a].H, this.b[a].Aa);
                this.c = [];
                this.b = []
            }
        };

        function If(a, b, c) {
            Jf.push(function () {
                if (c && "function" == typeof c) {
                    try {
                        var a = c(this.La)
                    } catch (f) {
                        b.a(f);
                        return
                    }
                    var e;
                    try {
                        e = a && a.then
                    } catch (f) {
                        b.a(f);
                        return
                    }
                    a instanceof Z ? a == b ? b.a(new TypeError("Chaining cycle detected")) : a.then(b.V.bind(b), b.a.bind(b)) : e ? Kf(a, e, b) : b.V(a)
                } else 1 == this.ga ? b.V(this.La) : b.a(this.La)
            }.bind(a));
            null == Lf && (Lf = Mf(Nf))
        }

        function Kf(a, b, c) {
            try {
                var d = !1;
                b.call(a, function (a) {
                    if (!d) {
                        d = !0;
                        var b;
                        try {
                            b = a && a.then
                        } catch (g) {
                            c.a(g);
                            return
                        }
                        b ? Kf(a, b, c) : c.V(a)
                    }
                }, c.a.bind(c))
            } catch (e) {
                c.a(e)
            }
        }

        function Nf() {
            for (; Jf.length;) {
                null != Lf && (Of(Lf), Lf = null);
                var a = Jf;
                Jf = [];
                for (var b = 0; b < a.length; ++b) a[b]()
            }
        }

        function Mf() {
            return 0
        }

        function Of() {
        }

        var Lf = null, Jf = [];
        $e(function () {
            window.Promise || (window.Promise = Z, window.Promise.resolve = Ef, window.Promise.reject = Ff, window.Promise.all = Gf, window.Promise.race = Hf, window.setImmediate ? (Mf = function (a) {
                return window.setImmediate(a)
            }, Of = function (a) {
                return window.clearImmediate(a)
            }) : (Mf = function (a) {
                return window.setTimeout(a, 0)
            }, Of = function (a) {
                return window.clearTimeout(a)
            }))
        });

        function Pf() {
            return {
                droppedVideoFrames: this.webkitDroppedFrameCount,
                totalVideoFrames: this.webkitDecodedFrameCount,
                corruptedVideoFrames: 0,
                creationTime: NaN,
                totalFrameDelay: 0
            }
        }

        $e(function () {
            if (window.HTMLVideoElement) {
                var a = HTMLVideoElement.prototype;
                !a.getVideoPlaybackQuality && "webkitDroppedFrameCount" in a && (a.getVideoPlaybackQuality = Pf)
            }
        });
    }.call(g, this));
    if (typeof(module) != "undefined" && module.exports) module.exports = g.shaka;
    else if (typeof(define) != "undefined" && define.amd) define(function () {
        return g.shaka
    });
    else this.shaka = g.shaka;
})();

