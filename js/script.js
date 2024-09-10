feather.replace()

$(document).ready(function () {
    $(".owl-carousel").owlCarousel();
});

$('.owl-carousel.testimonial-carousel').owlCarousel({
    loop: true,
    margin: 10,
    // autoplay:true,
    autoplayTimeout: 2000,
    autoplayHoverPause: true,
    nav: true,
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 3
        },
        1000: {
            items: 1
        }
    }
})

$('.owl-carousel.courses-carousel').owlCarousel({
    loop: true,
    margin: 10,
    // autoplay:true,
    autoplayTimeout: 2000,
    autoplayHoverPause: true,
    nav: true,
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 3
        },
        1000: {
            items: 4
        }
    }
})

var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl)
})

function openMegaMenu(evt, courseName) {
    evt.stopPropagation()
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.querySelectorAll(".tab-nav-new");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.querySelectorAll(".tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    if (document.getElementById(courseName)) {
        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(courseName).style.display = "block";
    }

    if (document.querySelector(".default-nav-list")) {
        document.querySelector(".default-nav-list").style.display = "none";
    }

    evt.currentTarget.className += " active";

    if (window.innerWidth < 1024) {
        document.querySelector(".main-default-list-link").style.display = "none"
    }
}

$('.dropdown-tabview').on('click', function (e) {
    e.stopPropagation();
});

$(".backToSubject").click(function (e) {
    e.stopPropagation();
    $(".main-default-list-link").css("display", "block")
    $(".default-nav-list").css("display", "block")
    $(".tab-nav-new").css("display", "none")
})

document.querySelectorAll(".tablinks").forEach(item => {
    item.addEventListener("click", function () {
        document.querySelector(".new-mega-layout").classList.add("bg-white")
    })
})

$(document).ready(function() {
    $("#miniCountryModal").modal("show");
});