import FormElementsToggle from "./FormElementsToggle";
import Swal from "sweetalert2";
import axios from "axios";
import cors from "cors";

class Api
{

  get(
    props
  )
  {
    this.url = props.url;
    this.loadingMessage = props.loadingMessage;
    this.confirm = props.confirm;
    this.quiet = props.quiet;
    this.debug = props.debug;

    return new Promise((resolve, reject) =>
    {
      if (this.quiet === true || props.confirm === false)
      {
        return this.fetch(this.debug).then((response) => resolve(response));
      } else
      {
        this.confirmMessage = "Êtes-vous sûr ?";
        if (typeof confirm === "string")
        {
          this.confirmMessage = confirm;
        }

        Swal.fire({
          title: this.confirmMessage,
          text: "Cet action est irreversible.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Oui.",
          cancelButtonText: "Non annuler !",
          reverseButtons: true,
        }).then((result) =>
        {
          if (result.isConfirmed)
          {
            return this.fetch(this.debug).then((response) => resolve(response));
          } else if (result.dismiss === Swal.DismissReason.cancel)
          {
            Swal.fire("Annulé", "L'opération a bien été annulée.", "success");
            resolve();
          }
        });
      }
    });
  }

  post(
    props
  )
  {
    this.url = props.url;
    this.loadingMessage = props.loadingMessage;
    this.data = props.data;
    this.confirm = props.confirm;
    this.quiet = props.quiet;
    this.debug = props.debug;

    if (props.headers)
    {
      this.headers = {
        headers: props.headers,
      }
    }

    if (props.debug === true)
    {
      console.log('Props: ');
      console.log(props);
    }

    return new Promise((resolve, reject) =>
    {
      if (this.quiet === true || props.confirm === false)
      {
        return this.send(props.data, props.debug).then((response) => resolve(response));
      }
      else
      {
        this.confirmMessage = "Êtes-vous sûr ?";
        if (typeof confirm === "string")
        {
          this.confirmMessage = confirm;
        }

        Swal.fire({
          title: this.confirmMessage,
          text: "Cet action est irreversible.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Oui.",
          cancelButtonText: "Non annuler !",
          reverseButtons: true,
        }).then((result) =>
        {
          if (result.isConfirmed)
          {
            return this.send(props.data, props.debug, this.headers).then((response) => resolve(response));
          } else if (result.dismiss === Swal.DismissReason.cancel)
          {
            Swal.fire("Annulé", "L'opération a bien été annulée.", "success");
            resolve();
          }
        });
      }
    });
  }

  send(data, debug)
  {
    return new Promise((resolve, reject) =>
    {
      let timeout = 0;
      if (this.quiet === false)
      {
        this.handleLoadingModal();
        timeout = 500;
      }

      setTimeout(() =>
      {
        cors();
        return axios.post(this.url, data)
          .then(response =>
          {
            let returnedData
            if (response.data)
            {
              returnedData = response.data;
            }
            else
            {
              returnedData = response;
            }
            if (debug === true)
            {
              console.log(returnedData);
            }

            if (this.quiet === false)
            {
              this.handleSuccessModal(returnedData);
            }

            resolve(returnedData);
          })
          .catch((error) =>
          {
            let returnedData;
            if (error.response)
            {
              returnedData = error.response.data;
            }
            else
            {
              returnedData = error;
            }
            if (this.debug === true)
            {
              console.error(returnedData);
            }
            if (this.quiet === false)
            {
              this.handleErrorModal(returnedData);
            }

            reject(data);
          });
      }, timeout);
    });
  }

  fetch(debug)
  {
    return new Promise((resolve, reject) =>
    {
      let timeout = 0;
      if (this.quiet === false)
      {
        this.handleLoadingModal();
        timeout = 500;
      }

      setTimeout(() =>
      {
        cors();
        return axios.get(this.url)
          .then(response =>
          {
            if (debug === true)
            {
              console.log(response.data);
            }

            if (this.quiet === false)
            {
              this.handleSuccessModal(response.data);
            }

            resolve(response.data);
          })
          .catch((error) =>
          {
            if (this.quiet === false)
            {
              this.handleErrorModal(error);
            }

            reject(error);
          });
      }, timeout);
    });
  }

  handleErrorModal(error)
  {
    if (this.debug === true)
    {
      console.error(error);
      fetch('/api/last-error', {
        method: 'GET',
        headers: {},
      })
        .then(function (response)
        {
          return response.json();
        })
        .then((body) =>
        {
          console.log('Debug info:');
          console.log("Date: " + body.date);
          console.log("Message: " + body.message);
          console.log("File: " + body.file);
        })
        .catch((error) =>
        {
          console.error("Cannot retrieve error log. ==> " + error);
        });
    }
    new FormElementsToggle(false);
    Swal.fire({
      title: "Une erreur est survenue.",
      text: error,
      type: "error",
      cancelButtonText: "Recharger la page",
      confirmButtonText: "Contacter le support",
      focusConfirm: false,
      showConfirmButton: true,
      showCancelButton: true,
    }).then((result) =>
    {
      if (result.isConfirmed)
      {
        window.location.href =
          "mailto:support@byniwee.io?subject=Erreur lors de l'ajout d'un client&body=J'ai une erreur lorsque je souhaite supprimer un client, la voici: " +
          error;
      }
      else
      {
        window.location.reload();
      }
    });
  }

  handleSuccessModal(body)
  {
    new FormElementsToggle(false);

    new Promise((resolve) =>
    {
      if (body.reload)
      {
        let timerInterval
        Swal.fire({
          title: "Succès !",
          html: "Redirection dans <b></b>",
          timer: 3000,
          timerProgressBar: true,
          icon: "success",
          didOpen: () =>
          {
            Swal.showLoading()
            const b = Swal.getHtmlContainer().querySelector('b')
            timerInterval = setInterval(() =>
            {
              let timeLeft = Swal.getTimerLeft() / 1000;
              b.textContent = timeLeft.toFixed(0);
            }, 100)
          },
          willClose: () =>
          {
            clearInterval(timerInterval)
          }
        }).then((result) =>
        {
          resolve();
          window.location.reload();
        })
      }
      else if (body.redirectTo)
      {
        let timerInterval
        Swal.fire({
          title: "Succès !",
          html: "Redirection dans <b></b>",
          timer: 3000,
          timerProgressBar: true,
          icon: "success",
          didOpen: () =>
          {
            Swal.showLoading()
            const b = Swal.getHtmlContainer().querySelector('b')
            timerInterval = setInterval(() =>
            {
              let timeLeft = Swal.getTimerLeft() / 1000;
              b.textContent = timeLeft.toFixed(0);
            }, 100)
          },
          willClose: () =>
          {
            clearInterval(timerInterval)
          }
        }).then((result) =>
        {
          resolve();
          window.location.href = body.redirectTo;

        })
      }
      else
      {
        Swal.fire(body.modaltitle, body.message, body.type);
        Swal.update({
          confirmButtonText: body.btnMessage,
        });
        resolve();
      }
    })
  }

  handleLoadingModal()
  {
    new FormElementsToggle(true);
    Swal.fire(this.loadingMessage);
    Swal.showLoading();
  }
}

export default Api;