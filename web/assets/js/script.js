var screenHeight = window.innerHeight;
$(".header").css("height",screenHeight);
$(".wrapper-header").css("height",screenHeight);
$(".wrapper-header").css("line-height",screenHeight + "px");

$( window ).resize(function() {
    var screenHeight = window.innerHeight;
    $(".header").css("height",screenHeight);
    $(".wrapper-header").css("height",screenHeight);
    $(".wrapper-header").css("line-height",screenHeight + "px");
});