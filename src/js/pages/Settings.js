import Api from "../core/Api";
class Settings
{
  constructor()
  {
    const regenerateApiKeyForm = document.querySelector(
      "#regenerate-api-key-form"
    );
    const clearSearchObjects = document.querySelector("#clear-search-objects");
    const fillSearchObjects = document.querySelector("#fill-search-objects");
    const reIndexSearch = document.querySelector("#re-index-search");
    const btns = document.querySelectorAll("button");

    if (reIndexSearch)
    {
      reIndexSearch.onclick = (e) =>
      {
        e.preventDefault();
        this.api.get(
          "/api/search-reindex",
          "POST",
          "Ré-indexation en cours...",
          null,
          false,
          false,
          false
        );
      };
    }

    if (clearSearchObjects)
    {
      clearSearchObjects.onclick = (e) =>
      {
        e.preventDefault();
        this.api.get(
          "/api/search-clear-index",
          "POST",
          "Vidage des index en cours...",
          null,
          false,
          false,
          false
        );
      };
    }

    if (fillSearchObjects)
    {
      fillSearchObjects.onclick = (e) =>
      {
        e.preventDefault();
        this.api.get(
          "/api/search-fill-index",
          "POST",
          "Remplissage des index en cours...",
          null,
          false,
          false,
          false
        );
      };
    }
  }
}

export default Settings;
