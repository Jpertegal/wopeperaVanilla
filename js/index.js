const loginBtn = document.getElementById("login");
const signupBtn = document.getElementById("signup");

loginBtn.addEventListener("click", (e) => {
  let parent = e.target.parentNode.parentNode;
  let signupParent = signupBtn.parentNode;

  if (!parent.classList.contains("slide-up")) {
    // Només pugi el formulari de login si està baixat i el formulari de sign-up està amunt
    if (!signupParent.classList.contains("slide-up")) {
      parent.classList.add("slide-up");
    }
  } else {
    signupParent.classList.add("slide-up");
    parent.classList.remove("slide-up");
  }
});

signupBtn.addEventListener("click", (e) => {
  let parent = e.target.parentNode;
  let loginParent = loginBtn.parentNode.parentNode;

  if (!parent.classList.contains("slide-up")) {
    // Només pugi el formulari de sign-up si està baixat i el formulari de login està amunt
    if (!loginParent.classList.contains("slide-up")) {
      parent.classList.add("slide-up");
    }
  } else {
    loginParent.classList.add("slide-up");
    parent.classList.remove("slide-up");
  }
});
