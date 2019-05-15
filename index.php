<!doctype html>
<html>
    <head>
        <title>MyShaderToy</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="/css/main/main.css">
        <link rel="stylesheet" href="/css/editor/editor.css">
    </head>
    <body>
        <table id="shaders-table">
            <!--Shaders will be loaded from database-->
        </table>
        <script type="text/javascript" src="/js/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="/js/database/database.js"></script>
        <script type="text/javascript">
            "use strict";
            $("document").ready(function() {
                /*Main work*/
                const Main = {
                    data: {
                        jq_table: $("#shaders-table"),

                        count: $("input[type=hidden]").val(),

                        ways: {
                            shaders_dir: "/editor/shaders/",

                            images_dir: "/editor/images/"
                        },

                        last_id: 1
                    },

                    library: {
                        load: id => {
                            //load image
                            $("#img_" + id).attr("src", Main.data.ways.images_dir + id + ".png");
                        },

                        createElement: (id) => {
                            return {
                                frame: $("<td>", {
                                    id: "frame_" + id
                                }),
                                ref: $("<a>", {
                                    id: "ref_" + id
                                }),
                                img: $("<img>", {
                                    id: "img_" + id,
                                    alt: "Title image don't load"
                                }),
                                id: id
                            }
                        },

                        createElementsFromInterval: (start_id, length) => {
                            let elements = [];

                            for (; start_id <= length; ++start_id)
                                elements.push(Main.library.createElement(start_id));

                            return elements;
                        },

                        createElementsFromIdArray: ids => {
                            let elements = [];

                            for (let i = 0; i < ids.length; ++i)
                                elements.push(Main.library.createElement(id[i]));

                            return elements;
                        }
                    },

                    update: count => {
                        Main.library.createElementsFromInterval(Main.data.last_id, count)
                            .forEach(item => {
                                if ((item.id - 1) % 5 === 0)
                                    Main.data.jq_table.append("<tr>");

                                item.frame.appendTo(Main.data.jq_table.children().last());
                                item.ref.appendTo(item.frame);
                                item.ref.on("click", function() {
                                    location.hash = item.id;
                                });
                                item.img.appendTo(item.ref);

                                Main.library.load(item.id);
                            });
                    },

                    init: load_queue_count => {
                        Main.update(load_queue_count);
                    }
                };
            Main.init(50);

            /*Editor work*/
            const Editor = {
                data: {
                    editor_elem: $("<iframe>", {
                        id: "editor-frame",
                    }),

                    back_btn: $("<button>", {
                        id: "close-btn",
                        class: "df",
                        style: "position: fixed; left: 10px; top: 10px; z-index: 2;",
                        html: "Close"
                    }),

                    fullscreen_btn: $("<button>", {
                        id: "fullscreen-btn",
                        class: "df",
                        style: "position: fixed; left: 120px; top: 10px; z-index: 2;",
                        html: "Fullscreen"
                    }),

                    is_fullscreen: false
                },

                open: function() {
                    Editor.data.editor_elem.attr("src", "editor" + location.hash);
                    Editor.data.editor_elem.prependTo("body");
                    Editor.data.editor_elem.addClass("opened");

                    Editor.data.back_btn.prependTo("body");
                    Editor.data.back_btn.on("click", function() {
                        Editor.close();
                    });

                    Editor.data.fullscreen_btn.prependTo("body");

                    Main.data.jq_table.css({
                        filter: "blur(10px)",
                        pointerEvents: "none"
                    });
                },

                close: function() {
                    Editor.data.editor_elem.detach();
                    Editor.data.back_btn.detach();
                    Editor.data.fullscreen_btn.detach();
                    Main.data.jq_table.css({
                        filter: "none",
                        pointerEvents: "auto"
                    });
                },

                init: function() {
                    /*td-elements (table > tr > td)*/
                    Main.data.jq_table.children().children().on("click", function () {
                        Editor.open();
                    });

                    Editor.data.back_btn.on("click", function() {
                        Editor.close();
                    });

                    Editor.data.fullscreen_btn.on("click", function() {
                        const duration = "50ms";

                        if (!Editor.data.is_fullscreen) {
                            Editor.data.editor_elem.animate({
                                left: 0,
                                top: 0,
                                width: "100%",
                                height: "100%"
                            }, {
                                duration: duration
                            });

                            Editor.data.back_btn.animate({
                                left: "330px"
                            }, {
                                duration: duration
                            });

                            Editor.data.fullscreen_btn.animate({
                                left: "451px"
                            }, {
                                duration: duration
                            });

                            Editor.data.is_fullscreen = true;
                        }
                        else {
                            Editor.data.editor_elem.animate({
                                left: "100px",
                                top: "100px",
                                width: innerWidth - 200 + "px",
                                height: innerHeight - 200 + "px"
                            }, {
                                duration: duration
                            });

                            Editor.data.back_btn.animate({
                                left: "10px"
                            }, {
                                duration: duration
                            });

                            Editor.data.fullscreen_btn.animate({
                                left: "120px"
                            }, {
                                duration: duration
                            });

                            Editor.data.is_fullscreen = false;
                        }
                    });

                    if (location.hash !== "")
                        Editor.open();
                }
            };
            Editor.init();
            });
        </script>
    </body>
</html>