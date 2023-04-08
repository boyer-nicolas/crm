import Api from "../core/Api";
import qs from "qs";
import Mobile from "../core/Mobile";
import SidebarHandler from "./SidebarHandler";
import SearchHandler from "./SearchHandler";
import Translator from "../core/Translator";
import DynamicImport from "./DynamicImport";
import DarkmodeHandler from "./DarkmodeHandler";

class PageHandler
{
  constructor()
  {
    new Translator();
    if (document.body.classList.contains("mobile"))
    {
      this.mobile = new Mobile();
    }
    this.api = new Api;
    this.search = new SearchHandler();
    let self = this;
    const skipUrls = [
      "/connexion",
    ];

    if (skipUrls.includes(window.location.pathname))
    {
      return;
    }

    this.pageContent = document.querySelector(".page-content");
    this.mobileHeader = document.querySelector(".navigation.mobile");
    this.loader = document.querySelector(".loader");

    window.addEventListener('popstate', function (event)
    {
      self.getPageContents();

    }, false);

    this.updateLinks();

    this.getPageContents();
  }

  getPageContents = async () =>
  {
    this.loader.classList.add("active", 'animate__animated', 'animate__fadeIn', 'animate__faster');
    this.pageContent.innerHTML = "";
    this.pageContent.classList.remove('animate__animated', 'animate__fadeIn', 'animate__faster');

    try
    {
      this.api.post({
        url: "/api/get-page-content",
        loadingMessage: null,
        data: qs.stringify({
          uri: window.location.pathname,
          device: "desktop",
        }),
        confirm: false,
        quiet: true,
        debug: false,
      }).then((pageContent) =>
      {
        if (pageContent.data === null)
        {
          if (pageContent.message)
          {
            this.pageContent.innerHTML = "Error: " + pageContent.message;
            this.removeLoader();
            this.pageContent.innerHTML = "<div class='mt-160 alert alert-danger'>" + pageContent.message + "</div>";
          }
          else
          {
          }
        }
        else
        {
          this.getEntryContent().then((entryContent) =>
          {
            if (document.body.classList.contains("mobile"))
            {
              let temp = document.createElement("div");
              temp.classList.add('container-fluid');
              temp.classList.add('mobile-header-info');
              temp.innerHTML = entryContent;
              let existing = document.querySelector(".mobile-header-info");
              if (existing)
              {
                existing.replaceWith(temp);
              }
              else
              {
                this.mobileHeader.append(temp);
              }
              this.pageContent.innerHTML = pageContent;
            }
            else
            {
              this.pageContent.innerHTML = entryContent;
              this.pageContent.innerHTML += pageContent;
              this.pageContent.classList.add('animate__animated', 'animate__fadeIn', 'animate__faster');
            }
            this.removeLoader();

            if (document.body.classList.contains("desktop"))
            {
              let pageTitle = document.querySelector(".title h1");
              const breadCrumbs = this.getBreadCrumbs();
              document.title = breadCrumbs[0].text + " - " + pageTitle.innerHTML;

              let breadcrumb = document.querySelector(".breadcrumb");

              if (breadcrumb)
              {
                let breadCumbsCount = breadCrumbs.length;

                let i = 1;
                breadCrumbs.forEach((part) =>
                {
                  if (i < breadCumbsCount)
                  {
                    breadcrumb.innerHTML += `<li class="breadcrumb-item"><a href="${part.link}">${part.text}</a></li>`;
                  }
                  else if (i == breadCumbsCount)
                  {
                    breadcrumb.innerHTML += `<li class="breadcrumb-item active" aria-current="page">${part.text}</li>`;
                  }
                  i++;
                });
              }
            }

            this.updateLinks();
            new SidebarHandler();
            new DarkmodeHandler();
            const goBackButton = document.querySelector("#goback-btn");
            if (goBackButton)
            {
              goBackButton.onclick = () =>
              {
                history.back();
              }
            }
            new Translator();
            new DynamicImport();
          });
        }
      })
        .catch((error) =>
        {
          this.removeLoader();
          this.pageContent.innerHTML = "Error: " + error;
        });
    }
    catch (error) 
    {
      this.removeLoader();
      this.pageContent.innerHTML = "Error: " + error;
    }
  }

  removeLoader()
  {
    this.loader.classList.remove("active", 'animate__animated', 'animate__fadeIn', 'animate__faster');
  }

  updateLinks()
  {
    const links = document.querySelectorAll("a");
    for (let i = 0; i < links.length; i++)
    {
      links[i].onclick = (e) =>
      {
        e.preventDefault();
        this.navigate(links[i]);
      }
    }
  }

  navigate(link)
  {
    if (this.mobile)
    {
      this.mobile.closeMenu();
    }

    window.history.pushState(null, null, link.href);
    this.getPageContents();
  }

  getEntryContent = async () => 
  {
    return new Promise((resolve, reject) =>
    {
      this.api.post({
        url: "/api/get-entry-content",
        loadingMessage: null,
        data: qs.stringify({
          uri: window.location.pathname,
        }),
        confirm: false,
        quiet: true,
        debug: false,
      }).then((response) =>
      {
        if (response.data === null)
        {
          if (response.message)
          {
            this.pageContent.innerHTML = "Error: " + response.message;
            return;
          }
          else
          {
            this.pageContent.innerHTML = "Page introuvable";
            return;
          }
        }
        else
        {
          return resolve(response);
        }
      })
        .catch((error) =>
        {
          this.pageContent.innerHTML = "Error: " + error;
          return reject(error);
        });
    });
  }

  getBreadCrumbs()
  {
    let here = location.href.split('/').slice(3);

    let parts = [{ "text": 'Trident byNiWee', "link": '/' }];

    for (let i = 0; i < here.length; i++)
    {
      let part = here[i];
      let text = part.charAt(0).toUpperCase() + part.slice(1)
      let link = '/' + here.slice(0, i + 1).join('/');
      if (text != '')
      {
        parts.push({ "text": text, "link": link });
      }
    }

    return parts;
  }
}

export default PageHandler;
