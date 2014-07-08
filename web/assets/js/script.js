// Set height of article to the window height
var screenHeight = window.innerHeight;
$(".header-home").css("height",screenHeight);
$(".wrapper-header").css("height",screenHeight);
$(".wrapper-header").css("line-height",screenHeight + "px");
$(".sign-in").css("line-height",screenHeight + "px");
$(".sign-up").css("line-height",screenHeight + "px");
$(".login").css("line-height",screenHeight + "px");
$( window ).resize(function() {
    var screenHeight = window.innerHeight;
    $(".header-home").css("height",screenHeight);
    $(".wrapper-header").css("height",screenHeight);
    $(".wrapper-header").css("line-height",screenHeight + "px");
    $(".sign-in").css("line-height",screenHeight + "px");
    $(".sign-up").css("line-height",screenHeight + "px");
    $(".login").css("line-height",screenHeight + "px");
});


//Popup Sign In
$(".sign-in-btn").click(function(){
    $(".sign-in").fadeIn("fast",function(){
        $(this).show();
    });
    $(".sign-back").fadeIn("fast",function(){
        $(this).show();
    });
    $("body").css("overflow","hidden");
});
$(".close-sign-in").click(function(){
    $(".sign-in").fadeOut("fast",function(){
        $(this).hide();
    });
    $(".sign-back").fadeOut("fast",function(){
        $(this).hide();
    });
    $("body").css("overflow","");
});
$(".sign-in-lk").click(function(){
    console.log("toto");
    $(".sign-up").fadeOut("fast",function(){$(this).hide();});
    $(".sign-in").fadeIn("fast",function(){$(this).show();});
});

//Popup Sign Up
$(".sign-up-btn").click(function(){
    $(".sign-up").fadeIn("fast",function(){
        $(this).show();
    });
    $(".sign-back").fadeIn("fast",function(){
        $(this).show();
    });
    $("body").css("overflow","hidden");
});
$(".close-sign-up").click(function(){
    $(".sign-up").fadeOut("fast",function(){
        $(this).hide();
    });
    $(".sign-back").fadeOut("fast",function(){
        $(this).hide();
    });
    $("body").css("overflow","");
});
$(".sign-up-lk").click(function(){
    console.log("toto");
    $(".sign-in").fadeOut("fast",function(){$(this).hide();});
    $(".sign-up").fadeIn("fast",function(){$(this).show();});
});

//Initialisation SelectOrDie
$(".language-select").selectOrDie({
    links: true,
});