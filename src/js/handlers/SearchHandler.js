"use strict";
import algoliasearch from "algoliasearch";

class Search
{
  constructor()
  {
    const searchBox = document.querySelector("#searchbox");
    const searchBtn = document.querySelector("#search-btn");

    if (!searchBox)
    {
      return;
    }

    if (!searchBtn)
    {
      return;
    }

    this.client = algoliasearch(
      "VG7N99GUA7",
      "c88a96c881b3f2916404f7c39ed8f024"
    );
    this.index = this.client.initIndex("pages");
    this.searchPopup = document.querySelector(".search-popup");
    this.searchBox = document.querySelector("#searchbox");
    this.searchButton = document.querySelector("#search-btn");

    searchBtn.onclick = (e) =>
    {
      e.preventDefault();
      this.toggleModal();
    };

    searchBox.addEventListener("keyup", (e) =>
    {
      if (e.key != "ArrowDown" && e.key != "ArrowUp")
      {
        this.start(e.target.value);
      }
    });

    document.addEventListener("keydown", (e) =>
    {
      if (e.ctrlKey && e.key === "k")
      {
        e.preventDefault();
        this.toggleModal();
      } else if (e.key === "Escape")
      {
        this.closeResults();
        this.clearQuery();
        this.clearResults();
      }
    });

    document.onclick = (e) =>
    {
      if (e.target.id !== "searchbox" && e.target.id !== "search-btn" && e.target.parentNode.id !== "search-btn")
      {
        if (this.isSearchOpened())
        {
          if (
            !this.hasParent(e.target, "search-popup") &&
            !this.hasParent(e.target, "searchbox")
          )
          {
            this.closeResults();
            this.clearQuery();
            this.clearResults();
          }
        }
      }
    };
  }

  hasParent(element, classname)
  {
    if (element.classList.contains(classname) || element.id === classname)
    {
      return true;
    } else
    {
      if (element.parentElement)
      {
        return this.hasParent(element.parentElement, classname);
      }
    }
  }

  isSearchOpened()
  {
    if (this.searchPopup.classList.contains("active"))
    {
      return true;
    } else
    {
      return false;
    }
  }

  start(query)
  {
    this.query = query;
    if (this.query.length)
    {
      this.search();
    } else
    {
      if (this.searchResults)
      {
        this.searchResults.innerHTML = `<p class="text-center">Aucun résultat trouvé.</p>`;
      }
    }

    this.searchResults = document.querySelector("#search-results");
    this.resultsCount = this.searchPopup.querySelector(".results-count");
  }

  clearQuery()
  {
    this.searchBox.value = "";
  }

  toggleModal()
  {
    if (this.isSearchOpened())
    {
      this.closeResults();
    }
    else
    {
      this.openResults();
    }
  }

  closeResults()
  {
    this.searchPopup.classList.remove("active");
    setTimeout(() =>
    {
      this.searchBox.blur();
    }, 300);
  }

  openResults()
  {
    this.searchPopup.classList.add("active");
    setTimeout(() =>
    {
      this.searchBox.focus();
    }, 300);
  }

  search()
  {
    this.index.search(this.query).then(({ hits }) =>
    {
      this.resultsCount.innerHTML = hits.length;
      this.results = hits;
      this.clearResults();
      this.populate();
      this.navigate();
    });
  }

  close()
  {
    this.closeResults();
    this.clearQuery();
    this.clearResults();
  }

  clearResults()
  {
    if (this.searchResults)
    {
      this.searchResults.innerHTML = "";
    }
  }

  populate()
  {
    if (this.results.length)
    {
      this.searchResults.innerHTML += `<ul>`;
      this.searchResultsUl = this.searchResults.querySelector("ul");
      let id = 0;
      for (let i = 0; i < this.results.length; i++)
      {
        this.searchResultsUl.innerHTML +=
          `<a href="${this.results[i].url}" class="d-flex flex-column mb-3 p-3 shadow rounded search-result" id="search-result-` +
          id +
          `">${this.results[i].title}<p>` +
          this.results[i].category +
          `</p></a>`;
        id++;
      }
      this.searchResults.innerHTML += `</ul>`;
    } else
    {
      this.searchResults.innerHTML = `<p class="text-center">Aucun résultat trouvé.</p>`;
    }
  }

  navigate()
  {
    let searchResults = document.querySelectorAll(".search-result");

    let firstResult = searchResults[0];
    if (firstResult)
    {
      firstResult.classList.add("selected");

      let activeElement = firstResult;

      document.addEventListener("keydown", (e) =>
      {
        if (e.key === "ArrowDown")
        {
          e.preventDefault();
          activeElement.classList.remove("selected");
          if (activeElement.nextSibling)
          {
            activeElement = activeElement.nextSibling;
          } else
          {
            activeElement = searchResults[0];
          }
          activeElement.classList.add("selected");
        } else if (e.key === "ArrowUp")
        {
          e.preventDefault();
          activeElement.classList.remove("selected");
          if (activeElement.previousSibling)
          {
            activeElement = activeElement.previousSibling;
          }
          else
          {
            activeElement = searchResults[searchResults.length - 1];
          }
          activeElement.classList.add("selected");
        }
        else if (e.key === "Enter")
        {
          e.preventDefault();
          this.close();
        }
        else if (e.key === "Escape")
        {
          this.closeResults();
          this.clearQuery();
          this.clearResults();
        }
      });
    }
  }
}

export default Search;
