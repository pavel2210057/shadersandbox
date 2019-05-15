"use strict";
const DatabaseHandler = {
    /*Cached data*/
    responses: {},

    push: (filename, content) => {
        $.ajax({
            url: "/editor/WriteDatabase.php",
            method: "POST",
            data: {
                filename: filename,
                content: content
            }
        }).done(function() { console.log("Success") });
    },

    load: (filename, callback) => {
        if(DatabaseHandler.responses[filename] === undefined)
            return $.ajax({url: filename}).done(response => {
                DatabaseHandler.responses[filename] = response;
                callback.call(this, response);
            });
        else
            callback.call(this, DatabaseHandler.responses[filename]);
    }
};