$(function () { // wait for document ready
    var flightpath = {
        entry : {
            curviness: 5.25,
            // autoRotate: true,
            values: [
                {x: 0,	y: 0},
                // {x: 100,	y: 150},
                {x: $(window).width()-400,	y: -200}
            ]
        }
    };
    // init controller
    var controller = new ScrollMagic.Controller();

    // create tween
    var tween = new TimelineMax()
        .add(TweenMax.to($("#plane"), 20, {css:{bezier:flightpath.entry}, ease:Power0.easeNone}))

    // build scene
    var scene = new ScrollMagic.Scene({triggerElement: "#trigger", duration: $(".bg-map").height(), offset: 0})
        .setPin("#target")
        .setTween(tween)
        // .addIndicators() // add indicators (requires plugin)
        .addTo(controller);

    $('.latest-posts-widget, .latest-events-widget').on('widget-resize', function() {
        scene.duration($(".bg-map").height());
    });

    $('.latest-posts-widget, .latest-events-widget').on('widget-resize-start', function() {
        $("#plane").fadeOut('fast');
    });

    $('.latest-posts-widget, .latest-events-widget').on('widget-resize-end', function() {
        $("#plane").fadeIn('fast');
    });
});