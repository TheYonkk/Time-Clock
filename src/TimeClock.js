import $ from 'jquery';
import {parse_json} from './parse_json';


function alertSuccess(selector, msg){
    selector.html("<div class=\"alert alert-success\" role=\"alert\">" + msg + "</div>")
        .hide().slideDown(1000).delay(2000).slideUp(1000);
}

function alertError(selector, msg){
    selector.html("<div class=\"alert alert-danger\" role=\"alert\"><strong>Error!</strong> " + msg + "</div>")
        .hide().slideDown(1000).delay(5000).slideUp(1000);
}

export const TimeClock = function(sel){

    var form = $(sel);
    form.submit(function(event) {
        event.preventDefault();

        console.log("Submitted");

        $.ajax({
            url: "post/timeclock.php",
            data: form.serialize(),
            method: "POST",
            success: function(data) {
                var json = parse_json(data);
                if(json.ok) {

                    alertSuccess($("#message"), json.message);

                    // if the override was shown, hide it
                    $(".clock-override").fadeOut(500);
                    $("input[type=checkbox]").prop("checked", false);

                    // uncheck the buttons
                    form.find("input[type=radio]").each(function(index){
                        $(this).prop("checked", false)
                    });

                } else {
                    // Login failed, a message is in json.message
                    alertError($("#message"), json.message);

                    // should we enable an override? If so, do
                    if (json.enableOverride){
                        $(".clock-override").fadeIn(500);

                    // no override required, we can uncheck the buttons
                    } else {
                        // uncheck the buttons
                        form.find("input[type=radio]").each(function(index){
                            $(this).prop("checked", false)
                        });
                    }

                }
            },
            error: function(xhr, status, error) {

                alertError($("#message"), error);

            }
        });



    });
}