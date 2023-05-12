let prevScrollpos = window.pageYOffset;
window.onscroll = function () {
  const currentScrollPos = window.pageYOffset;
  if (prevScrollpos > currentScrollPos) {
    document.getElementById("menuScroll").style.top = "0";
  } else {
    document.getElementById("menuScroll").style.top = "-60px";
  }
  prevScrollpos = currentScrollPos;
};

// ******************************

function showPass() {
  const passwords = document.querySelectorAll(".myPass");
  passwords.forEach((password) => {
    if (password.type === "password") {
      password.type = "text";
    } else {
      password.type = "password";
    }
  });
}

// ****************************

const scrollContainers = document.querySelectorAll(".scroll-media");
const viewScreen = window.matchMedia("(min-width: 990px)");

function scrollMedia(viewScreen) {
  if (viewScreen.matches) {
    scrollContainers.forEach((scrollContainer) => {
      scrollContainer.addEventListener("wheel", (e) => {
        e.preventDefault();
        scrollContainer.scrollLeft += e.deltaY * 2;
      });
    });
  }
}
scrollMedia(viewScreen);
viewScreen.addEventListener(scrollMedia);

// **************************
// Sélectionnez le bouton à l'aide de sa classe CSS
const btns = document.getElementsByClassName("btn-custom");

// Ajoutez un gestionnaire d'événements "click"
btns.forEach((btn) => {
  btn.addEventListener("click", function (event) {
    // Empêchez l'action par défaut de se produire
    event.preventDefault();
  });
});
