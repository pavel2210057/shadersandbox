<!doctype html>
<html>
    <head>
        <title>Editor</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="/css/codemirror/show-hint.css">
        <link rel="stylesheet" href="/css/codemirror/editor-theme.css">
        <link rel="stylesheet" href="/css/editor/editor.css">
        <link rel="stylesheet" href="/css/codemirror/codemirror.css">
    </head>
    <body>
        <!--GUI-->
        <div id="gui-panel">
            <button class="df" id="show">Show Code</button>
            <label for="zoom">Zoom</label>
            <input class="df" type="number" id="zoom">
            <button class="df" id="save">Save code</button>
        </div>

        <!--GLSL output-->
        <canvas id="glsl-output">Your browser don't supported canvas</canvas>

        <!--CodeMirror-->
        <textarea id="codemirror"></textarea>

        <script type="text/javascript" src="/js/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="/js/codemirror/codemirror.js"></script>
        <script type="text/javascript" src="/js/codemirror/show-hint.js"></script>
        <script type="text/javascript" src="/js/codemirror/matchbrackets.js"></script>
        <script type="text/javascript" src="/js/codemirror/clike.js"></script>
        <script type="text/javascript" src="/js/codemirror/glsl.js"></script>
        <script type="text/javascript">
            "use strict";
            $("document").ready(function() {
                /*App work*/
                let app = {
                    jq_elem: $("#glsl-output"),

                    glsl: undefined,

                    data: {
                        fps: 0,
                        vert_code: "attribute vec2 coords; void main() { gl_Position = vec4(coords, 0, 1); }",
                        frag_code: "#ifdef GL_ES\n" +
                            "precision mediump float;\n" +
                            "#endif\n" +
                            "\n" +
                            "#extension GL_OES_standard_derivatives : enable\n\n" +
                            "uniform float time;\n\n" +
                            "void main() {\n\tgl_FragColor = vec4(0.5);\n}",
                        uniform: {
                            time: 0,
                            mouse: { x: 0, y: 0 },

                            update: function() {
                                const delta = (1000 / app.data.fps) / 1000;
                                app.data.uniform.time += delta;
                                app.glsl.setUniform("time", app.data.uniform.time);
                                app.glsl.setUniform("resolution", innerWidth, innerHeight);
                                app.glsl.setUniform("mouse", app.data.uniform.mouse.x, app.data.uniform.mouse.y);
                            }
                        },
                        setZoom: zoom => {
                            app.jq_elem[0].width = innerWidth / zoom;
                            app.jq_elem[0].height = innerHeight / zoom;
                        }
                    },

                    init: function(fps, zoom = 1) {
                        /*data init*/
                        app.data.fps = fps;
                        app.data.setZoom(zoom);

                        /*codemirror init*/
                        cm.setValue(app.data.frag_code);

                        /*glsl init*/
                        app.glsl = new GLSL(app.jq_elem);
                        app.applyVertex();
                        app.applyFragment();
                        app.applyProgram();

                        /*screen update init*/
                        setInterval(function() {
                            app.render();
                        }, 1000 / app.data.fps);

                        /*events init*/
                        onmousemove = m => {
                            app.data.uniform.mouse.x = m.clientX;
                            app.data.uniform.mouse.y = m.clientY;
                        };
                        cm.on("change", function() {
                            app.data.frag_code = cm.getValue();
                            app.applyFragment();
                            app.applyProgram();
                        });
                        onresize = function() {
                            /*TODO resize event*/
                        }
                    },

                    applyVertex: function() {
                        app.glsl.setVertexCode(app.data.vert_code);
                        app.glsl.compileVertex();
                    },

                    applyFragment: function() {
                        app.glsl.setFragmentCode(app.data.frag_code);
                        app.glsl.compileFragment();
                    },

                    applyProgram: function() {
                        app.glsl.attach();
                        app.glsl.apply();
                    },

                    render: function() {
                        app.data.uniform.update();
                        app.glsl.render();
                    }
                };

                app.init(60);

                /*Database work*/
                let DatabaseHandler = {
                    handler: "FileHandler.php",

                    getFormDatabase: function() {
                        return DatabaseHandler.request({
                            action: "r",
                            filename: location.hash
                        });
                    },

                    pushToDatabase: function() {
                        return DatabaseHandler.request({
                            action: "w",
                            filename: location.hash === undefined ? -1 : location.hash.slice(1, location.hash.length),
                            content: cm.getValue()
                        });
                    },

                    request: request_data => {
                        let response = "";
                        $.ajax({
                            url: DatabaseHandler.handler,
                            method: "POST",
                            data: request_data
                        }).done(function(r) {
                            response = r;
                            console.log(r);
                        });
                        return response;
                    }
                };

                /*GUI work*/
                let GUI = {
                    show: $("#show"),
                    zoom: $("#zoom"),
                    save: $("#save"),

                    init: function() {
                        GUI.show.on("click", function () {
                            $("div.CodeMirror").toggleClass("hide");
                        });

                        GUI.save.on("click", function() {
                            DatabaseHandler.pushToDatabase();
                        });
                    }
                };

                GUI.init();
            });
        </script>
    </body>
</html>