"use strict";
    /*Codemirror settings*/
    let cm;
    function initCodemirror() {
        cm = CodeMirror.fromTextArea(codemirror, {
            lineNumbers: true,
            matchBrackets: true,
            theme: "darcula",
            mode: "x-shader/x-fragment"
        });

        const mac = (CodeMirror.keyMap.default == CodeMirror.keyMap.macDefault);
        CodeMirror.keyMap.default[(mac ? "Cmd" : "Ctrl") + "-Space"] = "autocomplete";
    }

initCodemirror();

/*GLSL*/
    class GLSL {
        constructor(jq_elem) {
            this.glsl = {};
            this.glsl.context = jq_elem[0].getContext("webgl");
            this.glsl.code = {
                vert: "",
                frag: ""
            };
            this.glsl.shader = {
                vert: this.glsl.context.createShader(this.glsl.context.VERTEX_SHADER),
                frag: this.glsl.context.createShader(this.glsl.context.FRAGMENT_SHADER)
            };
            this.glsl.program = this.glsl.context.createProgram();
            this.glsl.vertices = [
                 1.0,  1.0,
                -1.0,  1.0,
                -1.0, -1.0,
                 1.0, -1.0
            ];
            this.glsl.buffer = this.glsl.context.createBuffer();
            this.glsl.context.bindBuffer(this.glsl.context.ARRAY_BUFFER, this.glsl.buffer);
            this.glsl.context.bufferData(
                this.glsl.context.ARRAY_BUFFER,
                new Float32Array(this.glsl.vertices),
                this.glsl.context.DYNAMIC_DRAW
            );

            this.glsl.coords = this.glsl.context.getAttribLocation(this.glsl.program, "coords");
            this.getLocation();
        }

        setVertexCode(code) { this.glsl.code.vert = code; }

        setFragmentCode(code) { this.glsl.code.frag = code; }

        setUniform(name, x, y = undefined, z = undefined, w = undefined) {
            let uniform = this.glsl.context.getUniformLocation(this.glsl.program, name);

            if (y === undefined)
                this.glsl.context.uniform1f(uniform, x);
            else if (z === undefined)
                this.glsl.context.uniform2f(uniform, x, y);
            else if (w === undefined)
                this.glsl.context.uniform3f(uniform, x, y, z);
            else
                this.glsl.context.uniform4f(uniform, x, y, z, w);
        }

        compileVertex() {
            this.glsl.context.shaderSource(this.glsl.shader.vert, this.glsl.code.vert);
            this.glsl.context.compileShader(this.glsl.shader.vert);

            return this.glsl.context.getShaderInfoLog(this.glsl.shader.vert);
        }

        compileFragment() {
            this.glsl.context.shaderSource(this.glsl.shader.frag, this.glsl.code.frag);
            this.glsl.context.compileShader(this.glsl.shader.frag);

            return this.glsl.context.getShaderInfoLog(this.glsl.shader.frag);
        }

        getLocation() {
            this.glsl.context.vertexAttribPointer(
                this.glsl.coords,
                2,
                this.glsl.context.FLOAT,
                false,
                0,
                0);
            this.glsl.context.enableVertexAttribArray(this.glsl.coords);

        }

        attach() {
            this.glsl.context.attachShader(this.glsl.program, this.glsl.shader.vert);
            this.glsl.context.attachShader(this.glsl.program, this.glsl.shader.frag);
            this.glsl.context.linkProgram(this.glsl.program);

            return this.glsl.context.getProgramInfoLog(this.glsl.program);
        }

        apply() {
            this.glsl.context.useProgram(this.glsl.program);
        }

        render() {
            this.glsl.context.clearColor(0, 0, 0, 0);
            this.glsl.context.clear(this.glsl.context.COLOR_BUFFER_BIT);
            this.glsl.context.drawArrays(this.glsl.context.TRIANGLE_FAN, 0, 4);
        }
    }