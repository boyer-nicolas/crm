class Mobile
{
  constructor()
  {
    this.links = document.getElementsByTagName("a");
    this.burgerMenuBtn = document.querySelector(".menu-btn");
    this.menuContents = document.querySelector(".menu-contents");

    if (!this.menuContents)
    {
      return;
    }

    this.burgerMenuBtn.onclick = (e) =>
    {
      e.preventDefault();
      this.toggleMenu();
    }

  }

  toggleMenu()
  {
    this.burgerMenuBtn.classList.toggle("opened");
    this.burgerMenuBtn.setAttribute(
      "aria-expanded",
      this.burgerMenuBtn.classList.contains("opened")
    );
    this.burgerMenuBtn.parentNode.parentNode.parentNode.classList.toggle("opened");
    document.body.classList.toggle("no-scroll");
  }

  closeMenu()
  {
    this.burgerMenuBtn.classList.remove("opened");
    this.burgerMenuBtn.setAttribute(
      "aria-expanded",
      this.burgerMenuBtn.classList.contains("opened")
    );
    this.burgerMenuBtn.parentNode.parentNode.parentNode.classList.remove("opened");
    document.body.classList.remove("no-scroll");
  }

  openMenu()
  {
    this.burgerMenuBtn.classList.add("opened");
    this.burgerMenuBtn.setAttribute(
      "aria-expanded",
      this.burgerMenuBtn.classList.contains("opened")
    );
    this.burgerMenuBtn.parentNode.parentNode.parentNode.classList.add("opened");
    document.body.classList.add("no-scroll");
  }
}

export default Mobile;