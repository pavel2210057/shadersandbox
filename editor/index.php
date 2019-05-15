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
            <select class="df" id="zoom">
                <option value="0.5">0.5</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="4">4</option>
                <option value="8">8</option>
            </select>
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
        <script type="text/javascript" src="/js/database/database.js"></script>
        <script type="text/javascript">
            "use strict";
            $("document").ready(function() {
                /*App work*/
                const app = {
                    jq_elem: $("#glsl-output"),

                    glsl: undefined,

                    data: {
                        fps: 0,

                        shaders_dir: "/editor/shaders/",

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

                        };

                        /*hide the code before*/
                        $("div.CodeMirror").addClass("hide");

                        /*load the code*/
                        app.loadCode(location.hash.substr(1, location.href.length));
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
                    },

                    loadCode: id => {
                        $.ajax({ url: app.data.shaders_dir + id }).done(response => {
                            cm.setValue(response);
                            console.log("CODE LOAD OK");
                        });
                    }
                };
                app.init(60);

                /*GUI work*/
                const GUI = {
                    show: $("#show"),
                    zoom: $("#zoom"),
                    save: $("#save"),

                    init: function() {
                        GUI.show.on("click", function () {
                            $("div.CodeMirror").toggleClass("hide");
                        });

                        GUI.save.on("click", function() {
                            DatabaseHandler.push(app.data.shaders_dir + location.hash.slice(1, location.hash.length), cm.getValue());
                        });

                        GUI.zoom.on("change", option => {
                            console.log(option);
                            app.data.setZoom(option.target.value);
                        });
                    }
                };
                GUI.init();
            });
        </script>
    </body>
</html>