class FormElementsToggle {
  constructor(disabled = false) {
    var inputs = document.getElementsByTagName("input");
    for (var i = 0; i < inputs.length; i++) {
      inputs[i].disabled = disabled;
    }
    var selects = document.getElementsByTagName("select");
    for (var i = 0; i < selects.length; i++) {
      selects[i].disabled = disabled;
    }
    var textareas = document.getElementsByTagName("textarea");
    for (var i = 0; i < textareas.length; i++) {
      textareas[i].disabled = disabled;
    }
    var buttons = document.getElementsByTagName("button");
    for (var i = 0; i < buttons.length; i++) {
      buttons[i].disabled = disabled;
    }
  }
}

export default FormElementsToggle;
