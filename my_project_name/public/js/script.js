//mes images

/*var images = new Array();
images.push("../images/banner_woman.jpg");
images.push("../images/crystal-glass.jpg");
images.push("../images/denimjacket.jpg");

var pointeur = 0;

function ChangerImage() {
    document.getElementById("masuperimage").src = images[pointeur];

    if (pointeur < images.length - 1) {
        pointeur++;
    } else {
        pointeur = 0;
    }

    setTimeout("ChangerImage()", 5000)
}*/

// fonction pour changer l'image top banner dans l'index
window.onload = function() {
    ChangerImage();
    };

var images = [
    "images/woman-xl_left2.jpg.jpg",
    "images/hands_up_banner.jpg",
    "images/guitarist.jpg"
];
var index = 0;

$(function() {
    setInterval(changeHeader, 1000);
});

function changeHeader() {
    //Vérifier si on est a la dernière image
    if (index == images.length - 1) {
        index = 0;
    } else {
        index++;
    }
    $("body").index("background-image", "url(" + images[index] + ")");
}