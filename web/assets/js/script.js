// Set height of article to the window height
var screenHeight = window.innerHeight;
$(".header").css("height",screenHeight);
$(".wrapper-header").css("height",screenHeight);
$(".wrapper-header").css("line-height",screenHeight + "px");
$(".login").css("line-height",screenHeight + "px");
$( window ).resize(function() {
    var screenHeight = window.innerHeight;
    $(".header").css("height",screenHeight);
    $(".wrapper-header").css("height",screenHeight);
    $(".wrapper-header").css("line-height",screenHeight + "px");
    $(".login").css("line-height",screenHeight + "px");
});


//Popup Sign in

$(".signIn").click(function(){
    $(".login").fadeIn("100",function(){
        $(this).show();
    });
    $(".login-back").fadeIn("100",function(){
        $(this).show();
    });
    $("body").css("overflow","hidden");
});

$(".close-login").click(function(){
    $(".login").fadeOut("100",function(){
        $(this).hide();
    });
    $(".login-back").fadeOut("100",function(){
        $(this).hide();
    });
    $("body").css("overflow","");
});
