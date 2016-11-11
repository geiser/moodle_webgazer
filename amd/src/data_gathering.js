/**
 * WebGazer as a moodle block
 *
 * @package    block_webgazer
 * @copyright  Geiser Chalco <geiser@usp.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_webgazer/data_gathering
 */
define(["jquery"], function($) {

    var gazerData = [];
    var mouseTrackingStartTime = 0; // it's also used to indicate that mouse tracking is our webGazer
    var params = {autosavetime:0, enablescreenshot: false,
                  showpredictionpoint: false, showvideocanvas: false};
    
    return /** @alias module:block_webgazer/data_gathering */ {

        /**
         * Set parameters for webgazer
         */
        setParameters: function(autosavetime, enablescreenshot,
                           showpredictionpoint, showvideocanvas) {
            params.autosavetime = autosavetime;
            params.enablescreenshot = enablescreenshot;
            params.showpredictionpoint = showpredictionpoint;
            params.showvideocanvas = showvideocanvas;
        },

        /**
         * Set facial recognition and regression libraries for webgazer
         *
         * @param {String} tracker the id of the tracker library
         * @param {String} regression the id of the regression model
         */
        setLibraries: function(tracker, regression) {
            webgazer.setTracker(tracker);
            webgazer.setRegression(regression);
        },

        /**
         * Initialize the data gathering.
         *
         * @param {Integer} id for the session in which the data will be achieved
         * @param {String} complete url for the ajax.php
         */
        init: function(sessionid, url) {
            
            // save a screenshot
            var saveSessionScreenshot = function() {
                html2canvas(document.body, {
                    onrendered: function(canvas) {
                        console.log('img: '+canvas.toDataURL("image/jpeg"));
                        $.ajax({
                            url: url,
                            data: {
                                sessionid: sessionid,
                                action: "savescreenshot",
                                screenshot: canvas.toDataURL("image/jpeg", 0.5)
                            },
                            method: "POST"
                        }).done(function(msg) {
                            console.log('savescreenshot: '+msg);
                        });
                    }
                });
            };

            // setup the web-gazer video canvas
            var setupWebGazerVideoCanvas = function() {
                if (!webgazer.isReady() || 
                    document.getElementById('webgazerVideoFeed') == null ||
                    document.getElementById('webgazerVideoFeed') == undefined) {
                    setTimeout(setupWebGazerVideoCanvas, 1000);
                }
                var width = 160;
                var height = 120;
                var topDist = '0px';
                var leftDist = '0px';
                
                var container = document.getElementById('webgazerVideoDiv');
                container.style.position = 'relative';

                var webgazerVideoFeed = document.getElementById('webgazerVideoFeed');
                container.appendChild(webgazerVideoFeed);
                
                webgazerVideoFeed.style.display = 'block';
                webgazerVideoFeed.style.position = 'absolute';
                webgazerVideoFeed.style.top = topDist;
                webgazerVideoFeed.style.left = leftDist;
                webgazerVideoFeed.width = width;
                webgazerVideoFeed.height = height;
                webgazerVideoFeed.style.margin = '0px';

                webgazer.params.imgWidth = width;
                webgazer.params.imgHeight = height;

                var overlay = document.createElement('canvas');
                overlay.id = 'webgazerOverlay';
                document.body.appendChild(overlay);
                
                $("#webgazerOverlay").appendTo("#webgazerVideoDiv");
                overlay.style.position = 'absolute';
                overlay.width = width;
                overlay.height = height;
                overlay.style.top = topDist;
                overlay.style.left = leftDist;
                overlay.style.margin = '0px';

                var cl = webgazer.getTracker().clm;
                function drawLoop() {
                    requestAnimFrame(drawLoop);
                    overlay.getContext('2d').clearRect(0,0,width,height);
                    if (cl.getCurrentPosition()) {
                        cl.draw(overlay);
                    }
                }
                drawLoop();

            };


            // setting mousetracking as the webgazer
            var setupMouseTrackingAsWebGazer = function() {
                mouseTrackingStartTime = $.now();
                $(document).mousemove(function(e) {
                    if (webgazer != undefined && webgazer.isReady()) return;
                    var elapsedTime = $.now() - mouseTrackingStartTime;

                    // Ugly hack to indicate that the webgazer is using
                    // mouse-tracking instead webcam
                    // TODO: Add one more field in the DB to inidicate the source of data
                    elapsedTime = -1*elapsedTime;
                    gazerData.push({x: e.pageX, y: e.pageY, time: elapsedTime});    
                });
            };

            var saveGazer = function(callback) {
                if (gazerData.length > 0) {
                    if (mouseTrackingStartTime == 0) {
                        webgazer.pause();
                    }
                    $.ajax({
                        url: url,
                        data: {
                            sessionid: sessionid,
                            action: "savegazerdata",
                            gazerdata: gazerData
                        },
                        method: "POST"
                    }).done(function(msg) {
                        gazerData = [];
                        callback();
                    }).fail(function() {
                        callback();
                    });
                }
            };

            // looper to save data from the webgazer
            var looperAutoSave = function() {
                saveGazer(function() {
                    if (mouseTrackingStartTime == 0) {
                        webgazer.resume();
                    }
                });
                setTimeout(looperAutoSave, params.autosavetime);
            };

            // save data gathering when a user blur the page
            $(window).blur(function() {
                saveGazer(function() {
                    // do nothing when finish
                });
            });

            // save data gathering when a user navigate away from the page
            $(window).unload(function(){
                saveGazer(function() {
                    // do nothing when finish
                });
            });

            // resume data gathering when a user focus the page
            $(window).focus(function(){
                if (mouseTrackingStartTime == 0) {
                    webgazer.resume();
                    // setupWebGazerVideoCanvas();
                }
            });

            // start data gathering after load windows
            $(window).load(function() {

                // take the session screenshot as image
                if (params.enablescreenshot) saveSessionScreenshot();

                // detect compatibility of browser
                //$('#webgazerVideoText').text("You need a compatible browser and webcam to gather data!");
                if (!webgazer.detectCompatibility()) {
                    $('#webgazerVideoText').text("Your browser is inconpatible with webgazer! "+
                            "So, we are using mouse position as webgazer.");
                    setupMouseTrackingAsWebGazer();
                }

                // try to use webcam as webgazer -> if fail load mousetracking as webgazer
                webgazer.setGazeListener(function(data, elapsedTime) {
                    if (data == null) return;
                    var xp = data.x + window.pageXOffset;
                    var yp = data.y + window.pageYOffset;
                    gazerData.push({x: xp, y: yp, time: elapsedTime});
                }).begin(function () { // callback function in fail case
                    $('#webgazerVideoText').text("You need a webcam to use webgazer! "+
                            "So, we are using mouse position as webgazer.");
                    setupMouseTrackingAsWebGazer();
                }).showPredictionPoints(params.showpredictionpoint);

                // load video cavas from the webgazer
                if (params.showvideocanvas) {
                    setTimeout(setupWebGazerVideoCanvas, 1000);
                } else {
                    var miniloop = function () {
                        if (webgazer.isReady()) {
                            $('#webgazerVideoText').text("Webgazer initialized!");
                        } else {
                            setTimeout(miniloop, 1000);
                        }
                    }
                    miniloop();
                }

                // start auto-save
                if (params.autosavetime > 0) looperAutoSave();

            });
        }
    };
});

