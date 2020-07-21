$(function () {
    // Sidebar
    var duration = 400;
    var dashboardEl = $('body.dashboard .dashboard-container').first();
    var asideEl = $('body.dashboard aside').first();

    $('#sidebar-toggle').on('click', function() {
        if (dashboardEl.hasClass('aside-closed')) {
            dashboardEl.toggleClass('aside-closed').toggleClass('aside-opened');
            if (window.outerWidth > 767) {
                // Desktop
                var asideWidth = (window.outerWidth > 1366) ? 390 : 300;
                asideEl.animate({
                    width: asideWidth + 'px'
                }, duration, 'linear');
                dashboardEl.find('header .navbar').animate({
                    left: asideWidth + 'px'
                }, duration, 'linear');
            } else {
                // mobile
                asideEl.animate({
                    width: window.outerWidth + 'px'
                }, duration, 'linear');
                dashboardEl.find('header .navbar').animate({
                    left: 0 + 'px'
                }, duration, 'linear');
                asideEl.addClass('scroll');
            }
        } else {
            dashboardEl.toggleClass('aside-closed').toggleClass('aside-opened');
            if (window.outerWidth > 767) {
                // Desktop
                asideEl.animate({
                    width: '62px'
                }, duration, 'linear');
                dashboardEl.find('header .navbar').animate({
                    left: '62px'
                }, duration, 'linear');
            } else {
                // mobile
                asideEl.animate({
                    width: '62px'
                }, duration, 'linear');
                dashboardEl.find('header .navbar').animate({
                    left: '62px'
                }, duration, 'linear');
                asideEl.removeClass('scroll');
            }
        }
    });

    // $('#sidebar-toggle').on('click', function() {
    //     if (dashboardEl.hasClass('aside-closed')) {
    //         dashboardEl.removeClass('aside-closed');
    //         var asideWidth = (window.outerWidth > 1366) ? 390 : 300;
    //         asideEl.animate({
    //             width: asideWidth + 'px'
    //         }, duration, 'linear');
    //         dashboardEl.find('header .navbar').animate({
    //             left: asideWidth + 'px'
    //         }, duration, 'linear');
    //     } else {
    //         dashboardEl.addClass('aside-closed');
    //         asideEl.animate({
    //             width: '62px'
    //         }, duration, 'linear');
    //         dashboardEl.find('header .navbar').animate({
    //             left: '62px'
    //         }, duration, 'linear');
    //     }
    // });
    // End Sidebar
});