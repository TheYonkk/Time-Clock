import $ from 'jquery';
import {parse_json} from './parse_json';


function alertSuccess(selector, msg){
    selector.html("<div class=\"alert alert-success\" role=\"alert\">" + msg + "</div>")
        .hide().slideDown(1000).delay(2000).slideUp(1000);
}

function alertWarning(selector, msg){
    selector.html("<div class=\"alert alert-danger\" role=\"alert\">" + msg + "</div>")
        .hide().slideDown(1000).delay(5000).slideUp(1000);
}

function alertError(selector, msg){
    selector.html("<div class=\"alert alert-danger\" role=\"alert\"><strong>Error!</strong> " + msg + "</div>")
        .hide().slideDown(1000).delay(5000).slideUp(1000);
}

export const Users = function(sel){

    var page = $(sel);

    var buttons = page.find("button");    // All of the form's edit buttons
    for(var b=0; b<buttons.length; b++){
        // Get a button
        var button = $(buttons.get(b));

        var action = $(button).attr("name");

        // Determine the user ID
        var id = button.val();
        console.log(action + " " + id);

        installButtonListener(button, id, action);
    }


    function installButtonListener(button, userID, action){

        button.click(function(){

            console.log("clicked");

            var data = {
                "user":userID,
                "action":action
            };

            $.ajax({
                url: "post/users.php",
                data: data,
                method: "POST",
                success: function(data) {
                    var json = parse_json(data);
                    if(json.ok) {

                        // success

                        // user wanted to delete
                        if (json.action === "delete"){

                            alertWarning($("#message"), json.message);
                            installButtonListener($("#confirm-delete"), userID, "confirm-delete");

                        } else if (json.action === "edit") {
                            // user wanted to edit
                            window.location.assign(json.page);

                        } else if (json.action === "confirm-delete"){
                            if (json.success){
                                alertSuccess($("#message"), json.message);
                            } else {
                                alertError($("#message"), json.message);
                            }

                        } else if (json.action === "reset-password"){
                            if (json.success){
                                alertSuccess($("#message"), json.message);
                            } else {
                                alertError($("#message"), json.message);
                            }
                        }


                    } else {
                        // failure
                        alertError($("#message"), json.message);
                    }
                },
                error: function(xhr, status, error) {

                    alertError($("#message"), error);

                }
            });
        });
    }


    function installConfirmDeleteListener(selector){
        selector.click(function(){
           console.log("confirm?");
        });
    }


}