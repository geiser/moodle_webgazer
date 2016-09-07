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

    var isScreenshot = false;
    var isVideoCanvas = false;
    var saveTime = 0;
    var gazerData = [];
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
            // setup video canvas and take screenshot when webgazer is ready
            var setupIfReady = function() {
                if (webgazer.isReady()) {
                    $('#webgazerVideoText').text('');
                    // show video canvas
                    if (!isVideoCanvas && params.showvideocanvas) {
                        var width = 160;
                        var height = 120;
                        var topDist = '0px';
                        var leftDist = '0px';
                        var container = document.getElementById('webgazerVideoDiv');
                        container.style.position = 'relative';

                        $('#webgazerVideoFeed').appendTo('#webgazerVideoDiv');
                        var video = document.getElementById('webgazerVideoFeed');
                        video.style.display = 'block';
                        video.style.position = 'absolute';
                        video.style.top = topDist;
                        video.style.left = leftDist;
                        video.width = width;
                        video.height = height;
                        video.style.margin = '0px';

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
                        isVideoCanvas = true;

                        // save screenshot
                        if (!isScreenshot && params.enablescreenshot) {
                            html2canvas(document.body, {
                                onrendered: function(canvas) {
                                    console.log('img: '+canvas.toDataURL("image/jpeg"));
                                    $.ajax({
                                        url: url,
                                        data: {
                                            sessionid: sessionid,
                                            action: "savescreenshot",
                                            screenshot: canvas.toDataURL("image/jpeg", 0.5)},
                                        method: "POST"
                                    }).done(function(msg) {
                                        console.log('savescreenshot: '+msg);
                                    });
                                }
                            });
                            isScreenshot = true;
                        }
                    }
                } else {
                    setTimeout(setupIfReady, 100);
                }
            };

            // save gazer location in the DB, arguments[0] is true for default
            var saveGazerLocation = function() {
                var resume = (arguments.length < 1) || arguments[0] == true;
                console.log('resume: '+resume+' - arguments: '+arguments);
                webgazer.pause();
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
                    if (resume) webgazer.resume();
                }).fail(function() {
                    if (resume) webgazer.resume();
                }).always(function() {
                    if (resume) webgazer.resume();
                });
            };

            // save data gathering when a user blur the page
            $(window).blur(function(){
                webgazer.pause();
                saveGazerLocation(false);
            });

            // save data gathering when a user navigate away from the page
            $(window).unload(function(){
                webgazer.pause();
                saveGazerLocation(false);
            });

            // resume data gathering when a user focus the page
            $(window).focus(function(){
                setupIfReady();
                webgazer.resume();
            });

            // start data gathering after load windows
            $(window).load(function() {
                $('#webgazerVideoText').text("You need a compatible browser and webcam to gather data!");
                if (!webgazer.detectCompatibility()) {
                    $('#webgazerVideoText').text("Your browser is inconpatible with webgazer!");
                    return;
                }

                webgazer.setGazeListener(function(data, elapsedTime) {
                    if (data == null) { return; }
                    var xp = data.x + window.pageXOffset;
                    var yp = data.y + window.pageYOffset;
                    gazerData.push({x: xp, y: yp, time: elapsedTime});
                    if (params.autosavetime > 0 &&
                        ((elapsedTime - saveTime) > params.autosavetime)) {
                       saveTime = elapsedTime;
                       saveGazerLocation(); 
                    }
                    //console.log("[xoffset,yoffset]: [" +
                    //    window.pageXOffset + ";" +
                    //    window.pageYOffset + "] - [x;y]: [" +
                    //    xp + ";" + yp + "] - elapsedTime: " + elapsedTime);
                }).begin(function () {
                    $('#webgazerVideoText').text("You need a webcam to use webgazer!");
                }).showPredictionPoints(params.showpredictionpoint);

                setTimeout(setupIfReady, 100);
            }); 
        }
    };
});

