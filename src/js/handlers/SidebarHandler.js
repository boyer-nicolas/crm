class SidebarHandler
{
  constructor()
  {
    const current = location.pathname.toString();

    /**
     * Handle general menu items
     */
    if (current === "") return;
    var menuItems = document.querySelectorAll(".nav-item a");
    for (var i = 0, len = menuItems.length; i < len; i++)
    {
      if (menuItems[i].pathname.toString() === current)
      {
        menuItems[i].parentNode.classList.add("active");
        menuItems[i].classList.add("active");

        // Open/close submenu if in current
        if (menuItems[i].parentNode.parentNode.classList.contains("collapse"))
        {
          menuItems[i].parentNode.parentNode.parentNode.classList.add("show");
        }
      }
      else
      {
        menuItems[i].parentNode.classList.remove("active");
        menuItems[i].classList.remove("active");
      }
    }

    /**
     * Handle mobile menu items
     */
    var mobileMenuItems = document.querySelectorAll(
      ".mobile-nav-bottom-wrapper .mobile-menu-item"
    );
    for (var i = 0, len = mobileMenuItems.length; i < len; i++)
    {
      if (mobileMenuItems[i].classList.contains("never-active")) continue;
      if (mobileMenuItems[i].pathname.toString() === current)
      {
        mobileMenuItems[i].parentNode.classList.add("active");
        mobileMenuItems[i].classList.add("active");
        if (mobileMenuItems[i].parentNode.parentNode.classList.contains("collapse"))
        {
          mobileMenuItems[i].parentNode.parentNode.parentNode.classList.add("show");
        }
      }
      else
      {
        mobileMenuItems[i].parentNode.classList.remove("active");
        mobileMenuItems[i].classList.remove("active");
      }
    }

    /**
     * Handle menu item open/close
     */
    var menuItems = document.querySelectorAll(".nav-item div a");
    for (var i = 0, len = menuItems.length; i < len; i++)
    {
      if (menuItems[i].pathname.toString() === current)
      {
        menuItems[i].parentNode.parentNode.parentNode.parentNode.classList.add("active");
        let parent = menuItems[i].parentNode.parentNode.parentNode.parentNode.querySelector('.nav-item-has-children-key');

        if (!parent)
        {
          return;
        }

        menuItems[i].classList.add("active");
        parent.classList.add('active');

        if (menuItems[i].parentNode.parentNode.parentNode.classList.contains("collapse"))
        {
          menuItems[i].parentNode.parentNode.parentNode.parentNode.classList.add("show");
        }
      }
    }

    /**
     * Handle chevron open close
     */
    document
      .querySelectorAll(".nav-item-has-children .chevron-container")
      .forEach(function (el)
      {
        el.addEventListener("click", function (e)
        {
          var chevronContainer = el;
          var chevron = chevronContainer.querySelector("i");
          chevron.classList.toggle("bx-chevron-down");
          chevron.classList.toggle("bx-chevron-up");
        });
      });
  }
}

export default SidebarHandler;
