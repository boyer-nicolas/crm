import Until from "../core/Until";
import Swal from "sweetalert2";
import Api from "../core/Api";
import Pristine from '../lib/pristinejs/src/pristine';
class Customers
{
  constructor()
  {
    let addCustomerFormHtml;
    const addCustomerForm = document.querySelector("#add-customer-form-contents");

    if (!addCustomerForm)
    {
      return;
    }
    else
    {
      addCustomerFormHtml = addCustomerForm.innerHTML;
    }

    const config = {
      // class of the parent element where the error/success class is added
      classTo: 'form-group',
      errorClass: 'has-danger',
      successClass: 'has-success',
      // class of the parent element where error text element is appended
      errorTextParent: 'form-group',
      // type of element to create for the error text
      errorTextTag: 'div',
      // class of the error text element
      errorTextClass: 'text-help'
    };


    const addCustomerBtn = document.querySelector("#add-customer-btn");

    addCustomerBtn.addEventListener("click", function (e)
    {
      Swal.fire({
        title: "Ajouter un Client",
        html: addCustomerFormHtml,
        confirmButtonText: 'Ajouter <i class="bx bxs-add-to-queue"></i>',
        cancelButtonText: "Annuler",
        customClass: {
          htmlContainer: "add-customer-form-swal",
        },
        focusConfirm: false,
        showConfirmButton: false,
        showCancelButton: true,
        showLoaderOnConfirm: true,
      });
    });

    new Until(".add-customer-form-swal").then((e) =>
    {
      const formContainer = e;
      const form = formContainer.querySelector("form");

      if (!form)
      {
        throw new Error("Form not found");
      }

      let validator = new Pristine(form, config);

      form.addEventListener("submit", function (e)
      {
        e.preventDefault();
        let valid = validator.validate();

        if (valid)
        {
          const api = new Api();
          api.post({
            url: "/api/add-customer",
            loadingMessage: "Ajout en cours...",
            data: form,
            confirm: false,
            quiet: false,
            debug: true
          }).then(function (response)
          {
            console.log(reponse);
          }).catch(function (error)
          {
            throw new Error(error);
          });
        }
      });
    });

    // Edit

    const editCustomerBtn = document.querySelectorAll(".action-update-btn");

    for (let i = 0; i < editCustomerBtn.length; i++)
    {
      editCustomerBtn[i].addEventListener("click", function (e)
      {
        e.preventDefault();
      });
    }

    // Delete

    const deleteCustomerBtn = document.querySelectorAll(".action-delete-btn");

    for (let i = 0; i < deleteCustomerBtn.length; i++)
    {
      deleteCustomerBtn[i].addEventListener("click", function (e)
      {
        e.preventDefault();

        this.api.get(
          "/api/delete-customer",
          "POST",
          "Suppression en cours...",
          {
            user_id: this.dataset.id,
          },
          "Voulez-vous vraiment super ce client ?"
        );
      });
    }

    const customersTable = document.querySelector("#customers-table");
    if (!customersTable)
    {
      return;
    }
    const customersTableBody = customersTable.querySelector("tbody");
    const customersTableRows = customersTableBody.querySelectorAll("tr");
    const tableCheckboxes = customersTable.querySelectorAll(
      ".custom-checkbox .custom-control-input"
    );
    const numberOfCheckboxesChecked = 0;
    const customersToDelete = [];
    const multipleDeleteBtn = document.querySelector("#multiple-delete-users");
    const multipleDeleteBtnCount =
      multipleDeleteBtn.querySelector("#users-to-delete");

    function anyCheckbox()
    {
      for (let i = 0; i < tableCheckboxes.length; i++)
      {
        if (tableCheckboxes[i].type == "checkbox")
        {
          if (tableCheckboxes[i].checked)
          {
            return true;
          }
        }
      }

      return false;
    }

    function toggleActionRow()
    {
      const actionRow = document.querySelector(".select-opts");
      if (anyCheckbox())
      {
        actionRow.style.opacity = 100;
      } else
      {
        actionRow.style.opacity = 0;
      }
    }

    function updateDeleteBtn()
    {
      multipleDeleteBtnCount.innerHTML = numberOfCheckboxesChecked;
    }

    for (let i = 0; i < customersTableRows.length; i++)
    {
      customersTableRows[i].addEventListener("click", function (e)
      {
        const rowCheckbox = this.querySelector('input[type="checkbox"]');
        if (rowCheckbox.checked)
        {
          rowCheckbox.checked = false;
          numberOfCheckboxesChecked--;
          customersToDelete.splice(
            customersToDelete.indexOf(rowCheckbox.value),
            1
          );
          updateDeleteBtn();
        } else
        {
          rowCheckbox.checked = true;
          numberOfCheckboxesChecked++;
          customersToDelete.push(rowCheckbox.value);
          updateDeleteBtn();
        }
        toggleActionRow();
      });
    }

    multipleDeleteBtn.addEventListener("click", function (e)
    {
      e.preventDefault();

      this.api.get(
        "/api/delete-multiple-customers",
        "POST",
        "Suppression en cours...",
        {
          user_ids: customersToDelete,
        },
        "Êtes-vous sûr de vouloir supprimer ces " +
        numberOfCheckboxesChecked +
        " clients ?"
      );
    });
  }
}

export default Customers;
