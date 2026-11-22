$ = jQuery;

function spaToast(options) {
  const settings = $.extend(
    {
      status: "info", // info | success | error | warning
      title: "",
      message: "",
      duration: 3500,
    },
    options
  );

  // Create container if missing
  if (!$("#spa-toast-container").length) {
    $("body").append('<div id="spa-toast-container"></div>');
  }

  const icons = {
    info: `<i class="ri-information-fill"></i>`,
    success: `<i class="ri-checkbox-circle-fill"></i>`,
    error: `<i class="ri-close-circle-fill"></i>`,
    warning: `<i class="ri-error-warning-fill"></i>`,
  };

  const $toast = $("<div/>", {
    class: `spa-toast spa-toast-${settings.status}`,
  });

  $toast.append(`
      <div class="spa-toast-icon">${icons[settings.status]}</div>
      <div class="spa-toast-content">
        ${
          settings.title
            ? `<div class="spa-toast-title">${settings.title}</div>`
            : ""
        }
        <div class="spa-toast-message">${settings.message}</div>
      </div>
      <div class="spa-toast-close">&times;</div>
    `);

  $("#spa-toast-container").append($toast);

  // Close on click
  $toast.on("click", ".spa-toast-close", function () {
    removeToast($toast);
  });

  // Auto close
  if (settings.duration > 0) {
    setTimeout(() => removeToast($toast), settings.duration);
  }

  function removeToast($el) {
    $el.addClass("hide");
    setTimeout(() => $el.remove(), 250);
  }
}

function closeModal() {
  $(".spaFieldModal").fadeOut(100);
  clearForm();
}

function openModal() {
  const $form = $(".spaModalForm");
  $form.find("#spaFieldType").attr("disabled", false);
  $(".spaFieldModal").css({
    display: "flex",
  });
}

function resetNotice() {
  $(".spaNotice").removeClass("spaSuccess");
  $(".spaNotice").removeClass("spaWarning");
  $(".spaNotice").removeClass("spaError");
  $(".spaNotice").hide();
}

function resetFieldDetails() {
  $(".spaFieldDetails").hide();
}

function closeDependents() {
  $(".spaDependentRules").removeClass("active");
}

function resetDependents() {
  $(".spaShowIfChecked").val("").change();
  $(".spaHideIfChecked").val("").change();
}

function createField() {
  resetNotice();
  const $form = $(".spaModalForm");
  const fieldType = $form.find("#spaFieldType").val();
  const fieldLabel = $form.find("#spaFieldLabel").val();
  const fieldName = $form.find("#spaFieldName").val();
  let fieldData = {};
  const options = [];
  if (
    fieldType == "select" ||
    fieldType == "radio" ||
    fieldType == "checkbox"
  ) {
    const optionRows = $form.find(".spaOptionRow");
    $(optionRows).each(function (index) {
      const optionLabel = $(this).find(".spaSelectOptionLabel").val();
      const optionValue = $(this).find(".spaSelectOptionValue").val();
      const optionPrice = $(this).find(".spaSelectOptionPrice").val();
      const childOptionRows = $(this).find(".spaChildOptionRow");

      if (optionLabel) {
        const children = [];

        if (childOptionRows.length > 0) {
          $(childOptionRows).each(function (index) {
            const childOptionUrl = $(this)
              .find(".spaChildOptionImageUrl")
              .val();
            const childOptionLabel = $(this)
              .find(".spaSelectOptionValue")
              .val();
            if (childOptionLabel) {
              children.push({
                image: childOptionUrl,
                label: childOptionLabel,
              });
            }
          });
        }
        options.push({
          label: optionLabel,
          value: optionValue ? optionValue.trim() : optionLabel.trim(),
          price: optionPrice !== "" ? parseFloat(optionPrice) : 0,
          children: children,
        });
      }
    });
  } else if (fieldType == "image-button") {
    const optionRows = $form.find(".spaOptionRow");
    $(optionRows).each(function (index) {
      const optionLabel = $(this).find(".spaImageOptionLabel").val();
      const optionUrl = $(this).find(".spaImageOptionUrl").val();
      const optionPrice = $(this).find(".spaImageOptionPrice").val();
      if (optionLabel) {
        options.push({
          label: optionLabel.trim(),
          image: optionUrl,
          price: optionPrice !== "" ? parseFloat(optionPrice) : 0,
        });
      }
    });
  } else if (fieldType == "textarea") {
    const textAreaRows = $("#spaTextareaRows").val();
    const textAreaChars = $("#spaTextareaCharacterLimits").val();
    const textAreaPrice = $("#spaTextareaPrice").val();

    options.push({
      rows: parseInt(textAreaRows),
      characters: parseInt(textAreaChars),
      price: textAreaPrice !== "" ? parseFloat(textAreaPrice) : 0,
    });
  } else if (fieldType == "text") {
    const textPrice = $("#spaTextPrice").val();

    options.push({
      price: textPrice !== "" ? parseFloat(textPrice) : 0,
    });
  }

  if (fieldType == "") {
    $(".spaNotice").show().html("Choose a field type.").addClass("spaError");
    return false;
  }

  if (fieldLabel == "") {
    $(".spaNotice")
      .show()
      .html("<b>Field Label</b> cannot be empty.")
      .addClass("spaError");
    return false;
  }

  if (fieldName == "") {
    $(".spaNotice")
      .show()
      .html("<b>Field Name</b> cannot be empty.")
      .addClass("spaError");
    return false;
  }

  switch (fieldType) {
    case "select":
      if (options.length == 0) {
        $(".spaNotice")
          .show()
          .html(
            "Atleast one option is necessary and label field cannot be empty."
          )
          .addClass("spaError");
        return false;
      }
      break;
    case "checkbox":
      if (options.length == 0) {
        $(".spaNotice")
          .show()
          .html(
            "Atleast one option is necessary and label field cannot be empty."
          )
          .addClass("spaError");
        return false;
      }
      break;
    case "radio":
      if (options.length == 0) {
        $(".spaNotice")
          .show()
          .html(
            "Atleast one option is necessary and label field cannot be empty."
          )
          .addClass("spaError");
        return false;
      }
      break;
    case "image-button":
      if (options.length == 0) {
        $(".spaNotice")
          .show()
          .html(
            "Atleast one option is necessary and label field cannot be empty"
          )
          .addClass("spaError");
        return false;
      }
      break;
    case "text":
      break;
    case "textarea":
      break;
    default:
      return false;
  }

  // Populate field Data;
  fieldData = {
    type: fieldType,
    label: fieldLabel,
    name: fieldName,
    options: options,
  };

  return fieldData;
}

function loadFields() {
  closeModal();
  $(".ruleFields").html(`<span class="spaLoader"></span>`).css({
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    paddingBlock: "20px",
  });
  $.ajax({
    url: spaLocal.ajaxUrl,
    type: "POST",
    data: {
      action: "load_all_fields",
      postId: $("#spaRuleId").val(),
      fieldData: $("#spaFieldsInput").val(),
    },
    success: function (res) {
      $(".ruleFields").html(res).css({
        display: "block",
        paddingBlock: "20px",
      });
    },
    error: function (err) {
      console.error(err.responseText);
    },
  });
}

function populateForm(data) {
  clearForm();
  const $form = $(".spaModalForm");
  $form.find("#spaFieldType").val(data.type).change().attr("disabled", true);
  $form.find("#spaFieldLabel").val(data.label).change();
  $form.find("#spaFieldName").val(data.name).change();
  if (
    data.type == "select" ||
    data.type == "checkbox" ||
    data.type == "radio"
  ) {
    if (data.options.length > 0) {
      $("#fieldOptions .spaOptionRows").html("");
      data.options.forEach((item, index) => {
        let row = `<div class="spaOptionRow">
                    <div class="spaOptionColumn">
                        <label for="" class="spaLabel">Label</label>
                        <input type="text" class="spaTextField spaSelectOptionLabel" value="${item.label}">
                    </div>
                    <div class="spaOptionColumn">
                        <label for="" class="spaLabel">Value</label>
                        <input type="text" class="spaTextField spaSelectOptionValue" value="${item.value}">
                    </div>
                    <div class="spaOptionColumn">
                        <label for="" class="spaLabel">Price</label>
                        <input type="number" class="spaTextField spaSelectOptionPrice" placeholder="Ex: 10.99" value="${item.price}">
                    </div>
                    <div class="spaOptionColumn">
                        <div class="spaFlex spaAlignCenter spaGap-1">
                            <button type="button" class="spaButtonIcon spaDeleteRow"><i class="ri-delete-bin-fill"></i></button>
                        </div>
                    </div>
                    <div class="spaChildOptionRows">
                        <button type="button" class="spaAddChildOption"><i class="ri-add-circle-line"></i></button>`;
        if (item.children && item.children.length > 0) {
          item.children.forEach((child, index) => {
            row += `<div class="spaChildOptionRow">
                            <div class="spaChildOptionColumn">
                                <label for="" class="spaLabel">Image</label>
                                <img class="spaChildOptionImage" src="${child.image}" />
                                <input type="hidden" value="${child.image}" class="spaChildOptionImageUrl">
                                <div class="spaChildOptionImageButtons">
                                    <button type="button" class="spaChildUploadImage spaSmallButton">Upload</button>
                                    <button type="button" class="spaChildDeleteImage spaSmallButton">Remove</button>
                                </div>
                            </div>
                            <div class="spaChildOptionColumn spaChildOptionColumnLabel">
                                <label for="" class="spaLabel">Label</label>
                                <input type="text" class="spaTextField spaSelectOptionValue" value="${child.label}">
                            </div>
                            <div class="spaChildOptionColumn">
                                <div class="spaFlex spaAlignCenter spaGap-1">
                                    <button type="button" class="spaButtonIcon spaDeleteChildRow"><i class="ri-delete-bin-fill"></i></button>
                                </div>
                            </div>
                        </div>`;
          });
        }
        row += `</div>
                </div>`;
        $("#fieldOptions .spaOptionRows").append(row);
      });
    }
  } else if (data.type == "image-button") {
    if (data.options.length > 0) {
      $("#imageFieldOptions .spaOptionRows").html("");
      data.options.forEach((item, index) => {
        const row = `<div class="spaOptionRow">
                        <div class="spaOptionColumn">
                            <label for="" class="spaLabel">Upload Image</label>
                            <img class="spaImagePreview" src="${item.image}"/>
                            <input type="hidden" value="${item.image}" class="spaImageOptionUrl">
                            <div class="spaImageBtns">
                                <button type="button" class="spaUploadImage spaSmallButton">Upload</button>
                                <button type="button" class="spaRemoveImage spaSmallButton">Remove</button>
                            </div>
                        </div>
                        <div class="spaOptionColumn">
                            <label for="" class="spaLabel">Label</label>
                            <input type="text" class="spaTextField spaImageOptionLabel" value="${item.label}">
                        </div>
                        <div class="spaOptionColumn">
                            <label for="" class="spaLabel">Price</label>
                            <input type="number" class="spaTextField spaImageOptionPrice" placeholder="Ex: 10.99" value="${item.price}">
                        </div>
                        <div class="spaOptionColumn">
                            <div class="spaFlex spaAlignCenter spaGap-1">
                                <button type="button" class="spaButtonIcon spaDeleteRow"><i class="ri-delete-bin-fill"></i></button>
                            </div>
                        </div>
                    </div>`;
        $("#imageFieldOptions .spaOptionRows").append(row);
      });
    }
  } else if (data.type == "text") {
    $("#spaTextPrice").val(data.options[0].price);
  } else if (data.type == "textarea") {
    $("#spaTextareaRows").val(data.options[0].rows);
    $("#spaTextareaCharacterLimits").val(data.options[0].characters);
    $("#spaTextareaPrice").val(data.options[0].price);
  }
}

function clearForm() {
  const form = $(".spaModalForm");
  form.find("input").val("").change();
  form.find("select").val("").change();
  form.find(".spaOptionRows").html("");
}

function deleteField(key) {
  $(".spaProgressLoader").addClass("active");
  $.ajax({
    url: spaLocal.ajaxUrl,
    type: "POST",
    data: {
      action: "delete_field_by_key",
      fieldData: $("#spaFieldsInput").val(),
      key: key,
    },
    success: function (res) {
      if (res.success) {
        $("#spaFieldsInput").val(res.data.serialized);
        $($(".spaSingleField")[res.data.key]).fadeOut(200);
        spaToast({
          status: "success",
          title: "Success",
          message: "Field Removed Successfully!",
        });
      }
      $(".spaProgressLoader").removeClass("active");
    },
    error: function (err) {
      console.error(err.responseText);
    },
  });
}

$(document).on("click", ".spaAddOption", function () {
  const row = `<div class="spaOptionRow">
                    <div class="spaOptionColumn">
                        <label for="" class="spaLabel">Label</label>
                        <input type="text" class="spaTextField spaSelectOptionLabel">
                    </div>
                    <div class="spaOptionColumn">
                        <label for="" class="spaLabel">Value</label>
                        <input type="text" class="spaTextField spaSelectOptionValue">
                    </div>
                    <div class="spaOptionColumn">
                        <label for="" class="spaLabel">Price</label>
                        <input type="number" class="spaTextField spaSelectOptionPrice" placeholder="Ex: 10.99">
                    </div>
                    <div class="spaOptionColumn">
                        <div class="spaFlex spaAlignCenter spaGap-1">
                            <button type="button" class="spaButtonIcon spaDeleteRow"><i class="ri-delete-bin-fill"></i></button>
                        </div>
                    </div>
                    <div class="spaChildOptionRows">
                        <button type="button" class="spaAddChildOption"><i class="ri-add-circle-line"></i></button>
                    </div>
                </div>`;
  $(this).closest(".spaFormGroup").find(".spaOptionRows").append(row);
});

$(document).on("click", ".spaAddImageOption", function () {
  const row = `<div class="spaOptionRow">
                        <div class="spaOptionColumn">
                            <label for="" class="spaLabel">Upload Image</label>
                            <img class="spaImagePreview" />
                            <input type="hidden" value="" class="spaImageOptionUrl">
                            <div class="spaImageBtns">
                                <button type="button" class="spaUploadImage spaSmallButton">Upload</button>
                                <button type="button" class="spaRemoveImage spaSmallButton">Remove</button>
                            </div>
                        </div>
                        <div class="spaOptionColumn">
                            <label for="" class="spaLabel">Label</label>
                            <input type="text" class="spaTextField spaImageOptionLabel">
                        </div>
                        <div class="spaOptionColumn">
                            <label for="" class="spaLabel">Price</label>
                            <input type="number" class="spaTextField spaImageOptionPrice" placeholder="Ex: 10.99">
                        </div>
                        <div class="spaOptionColumn">
                            <div class="spaFlex spaAlignCenter spaGap-1">
                                <button type="button" class="spaButtonIcon spaDeleteRow"><i class="ri-delete-bin-fill"></i></button>
                            </div>
                        </div>
                    </div>`;
  $(this).closest(".spaFormGroup").find(".spaOptionRows").append(row);
});

$(document)
  .on("click", ".spaDeleteRow", function () {
    $(this).closest(".spaOptionRow").remove();
  })
  .on("click", ".spaDeleteChildRow", function () {
    $(this).closest(".spaChildOptionRow").remove();
  });

$(document).on("click", ".spaSubmitFields", function () {
  const fieldData = createField();
  if (!fieldData) {
    return;
  }
  closeModal();
  $(".ruleFields").html(`<span class="spaLoader"></span>`).css({
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    paddingBlock: "20px",
  });
  $.ajax({
    url: spaLocal.ajaxUrl,
    type: "POST",
    data: {
      action: "create_new_field",
      fieldData: fieldData,
      postId: $("#spaRuleId").val(),
      isEdit: $(this).data("edit"),
    },
    success: function (res) {
      $("#spaFieldsInput").val(res.data);
      loadFields();
      clearForm();
      spaToast({
        status: "success",
        title: "Success",
        message: "Field Created.",
      });
    },
    error: function (err) {
      console.error(err.responseText);
    },
  });
});

$(document)
  .on("input", "#spaFieldName", function () {
    $(this).val(
      $(this)
        .val()
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "_")
    );
  })
  .on("input", "#spaFieldLabel", function () {
    $("#spaFieldName").val(
      $(this)
        .val()
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "_")
    );
  });

$(document).on("change", "#spaFieldType", function () {
  resetFieldDetails();
  const fieldType = $(this).val();
  if (fieldType == "select") {
    $("#fieldOptions").slideDown(200);
  } else if (fieldType == "checkbox") {
    $("#fieldOptions").slideDown(200);
  } else if (fieldType == "radio") {
    $("#fieldOptions").slideDown(200);
  } else if (fieldType == "textarea") {
    $("#textareaFieldOptions").slideDown(200);
  } else if (fieldType == "text") {
    $("#textFieldOptions").slideDown(200);
  } else if (fieldType == "image-button") {
    $("#imageFieldOptions").slideDown(200);
  }
});

// Media Uploader
$(document)
  .on("click", ".spaUploadImage", function (e) {
    let spaMediaUploader;
    e.preventDefault();

    const container = $(this);

    spaMediaUploader = wp.media({
      title: "Select Image",
      button: {
        text: "Use this image",
      },
      multiple: false,
    });
    spaMediaUploader.on("select", function () {
      const attachment = spaMediaUploader
        .state()
        .get("selection")
        .first()
        .toJSON();
      container
        .closest(".spaOptionColumn")
        .find(".spaImagePreview")
        .attr("src", attachment.url);
      container
        .closest(".spaOptionRow")
        .find(".spaImageOptionLabel")
        .val(attachment.title);
      container
        .closest(".spaOptionRow")
        .find(".spaImageOptionUrl")
        .val(attachment.url);
    });

    spaMediaUploader.open();
  })
  .on("click", ".spaChildUploadImage", function (e) {
    let spaMediaUploader;
    e.preventDefault();

    const container = $(this);

    spaMediaUploader = wp.media({
      title: "Select Image",
      button: {
        text: "Use this image",
      },
      multiple: false,
    });
    spaMediaUploader.on("select", function () {
      const attachment = spaMediaUploader
        .state()
        .get("selection")
        .first()
        .toJSON();
      container
        .closest(".spaChildOptionColumn")
        .find(".spaChildOptionImage")
        .attr("src", attachment.url);
      container
        .closest(".spaChildOptionColumn")
        .find(".spaChildOptionImageUrl")
        .val(attachment.url);
      container
        .closest(".spaChildOptionRow")
        .find(".spaSelectOptionValue")
        .val(attachment.title);
    });

    spaMediaUploader.open();
  });

$(document).on("click", ".spaRemoveImage", function () {
  $(this).closest(".spaOptionColumn").find(".spaImagePreview").attr("src", "");
  $(this).closest(".spaOptionColumn").find("input:hidden").val();
});

$(document).on("click", ".spaChildDeleteImage", function () {
  $(this)
    .closest(".spaChildOptionColumn")
    .find(".spaChildOptionImage")
    .attr("src", "");
  $(this).closest(".spaChildOptionColumn").find("input:hidden").val();
});

$(document).on("click", ".spaCloseModal", function () {
  closeModal();
});

$(document).on("click", ".spaAddField", function () {
  $(".spaSubmitFields").data("edit", "not-edit");
  openModal();
});

$(document).on("click", ".spaEditField", function () {
  $(".spaSubmitFields").data("edit", $(this).data("edit"));
  openModal();
  populateForm($(this).data("fields"));
});

$(document).on("click", ".spaDeleteField", function () {
  deleteField($(this).data("delete"));
});

$(document)
  .on("click", ".spaFieldCondition", function () {
    $(".spaFieldConditionRules").addClass("active");
    $(".spaConditions").html(`Loading...`);
    $(".spaSaveCondition").attr("data-key", $(this).data("key"));
    $.ajax({
      url: spaLocal.ajaxUrl,
      type: "POST",
      data: {
        action: "load_field_conditions",
        fieldData: $("#spaFieldsInput").val(),
        fieldKey: $(this).data("key"),
      },
      success: function (res) {
        const $conditionBox = $(".spaFieldConditionRules").find(
          ".spaConditions"
        );
        $conditionBox.html(res.data.data);
        console.log(res.data);
        $("#spaConditionRelation")
          .val(res.data.relation ? res.data.relation : "AND")
          .change();
      },
      error: function (err) {
        console.error(err.responseText);
      },
    });
  })
  .on("click", ".spaAddCondition", function () {
    let conditionRow = `<div class="spaConditionRow">
            <div class="spaConditionColumn">
                <div class="spaConditionGroup">
                    <label class="spaLabel">Field</label>
                    <input class="spaConditionField" type="text">
                </div>
            </div>
            <div class="spaConditionColumn">
                <div class="spaConditionGroup">
                    <label class="spaLabel">Condition</label>
                    <select class="spaCompareCondition">
                        <option value="equal">Is equal</option>
                        <option value="not-equal">Not equal</option>
                        <option value="empty">Is empty</option>
                        <option value="not-empty">Not empty</option>
                    </select>
                </div>
            </div>
            <div class="spaConditionColumn">
                <div class="spaConditionGroup">
                    <label class="spaLabel">Value</label>
                    <input class="spaCompareValue" type="text">
                </div>
            </div>
            <span class="spaRemoveCondition"><i class="ri-close-line"></i></span>            
            <span class="spaCorelateCondition"><i class="ri-links-line"></i></span>            
        </div>`;
    const $conditionBox = $(".spaFieldConditionRules").find(".spaConditions");
    $($conditionBox).append(conditionRow);
  })
  .on("click", ".spaFieldKey", function () {
    if (navigator.clipboard.writeText($(this).find("span").text())) {
      spaToast({
        status: "success",
        title: "Success",
        message: "Copied to clipboard",
      });
    }
  })
  .on("click", ".spaRemoveCondition", function () {
    $(this).closest(".spaConditionRow").remove();
  })
  .on("click", ".spaConditionClose", function () {
    $(".spaFieldConditionRules").removeClass("active");
  })
  .on("click", ".spaDependentsClose", function () {
    $(".spaDependentRules").removeClass("active");
  })
  .on("click", ".spaSaveCondition", function () {
    const conditions = [];
    let error = 0;
    $(".spaConditionRow").each(function (index) {
      const field = $(this).find(".spaConditionField").val();
      const compare = $(this).find(".spaCompareCondition").val();
      const value = $(this).find(".spaCompareValue").val();
      const corelate = $(this).find(".spaCorelateCondition").hasClass("active");
      if (field) {
        conditions.push({
          field: field,
          compare: compare,
          value: value,
          corelate: corelate ? "yes" : "no",
        });
      } else {
        error++;
      }
    });

    if (error > 0) {
      spaToast({
        status: "error",
        title: "Error",
        message: "Condition field cannot be empty",
      });
      return;
    }

    $.ajax({
      url: spaLocal.ajaxUrl,
      type: "POST",
      data: {
        action: "save_field_condition",
        fieldKey: $(this).attr("data-key"),
        fieldData: $("#spaFieldsInput").val(),
        conditions: conditions,
        relation: $("#spaConditionRelation").val(),
      },
      success: function (res) {
        if (res.success) {
          $("#spaFieldsInput").val(res.data.fieldData).change();
          $(".spaFieldConditionRules").removeClass("active");
          spaToast({
            status: "success",
            title: "Success",
            message: "Conditions saved successfully!",
          });
          spaToast({
            status: "info",
            title: "Notice",
            message: "Make sure to update the post to save the changes.",
          });
          $(".spaConditions").html(`Loading...`);
        } else {
          spaToast({
            status: "error",
            title: "Error",
            message: res.data,
          });
        }
      },
      error: function (err) {
        spaToast({
          status: "error",
          title: "Error",
          message: err.responseText,
        });
      },
    });
  })
  .on("click", ".spaCorelateCondition", function () {
    $(this).toggleClass("active");
  });

$(document).on("click", ".spaFieldDependents", function () {
  const key = $(this).data("key");
  const option = $(this).data("option");
  $(".spaDependentRules").addClass("active");
  $(".spaSaveDependents").attr("data-key", key);
  $(".spaSaveDependents").attr("data-option", option);
  $("#spaShowIfChecked").html("");
  $("#spaHideIfChecked").html("");
  $(".spaDependentOption").html("Loading option...");
  $.ajax({
    url: spaLocal.ajaxUrl,
    type: "POST",
    data: {
      action: "show_field_dependents",
      key: key,
      option: option,
      fieldData: $("#spaFieldsInput").val(),
    },
    success: function (res) {
      // console.log(res);
      // return;
      if (res.success) {
        $(".spaDependentOption").html(res.data.title);
        // $("#spaShowIfChecked").val(res.data.showIfChecked).change();
        // $("#spaHideIfChecked").val(res.data.showIfChecked).change();
        console.log(res.data.data);
        $("#spaShowIfChecked").html(res.data.showIfChecked);
        $("#spaHideIfChecked").html(res.data.hideIfChecked);
      } else {
        spaToast({
          status: "error",
          title: "Error",
          message: res.data,
        });
      }
    },
    error: function (err) {
      spaToast({
        status: "error",
        title: "Error",
        message: err.responseText,
      });
    },
  });
});

$(document).on("click", ".spaSaveDependents", function () {
  const showIfChecked = $("#spaShowIfChecked").val();
  const hideIfChecked = $("#spaHideIfChecked").val();
  const key = $(this).attr("data-key");
  const optionKey = $(this).attr("data-option");
  $(this).find(".spaBtnLoader").css({
    display: "inline-block",
  });

  console.log(key);
  console.log(optionKey);

  $.ajax({
    url: spaLocal.ajaxUrl,
    type: "POST",
    data: {
      action: "load_field_dependents",
      fieldData: $("#spaFieldsInput").val(),
      key: key,
      showIfChecked: showIfChecked,
      hideIfChecked: hideIfChecked,
      option: optionKey,
    },
    success: function (res) {
      if (res.success) {
        $(".spaSaveDependents").find(".spaBtnLoader").css({
          display: "none",
        });
        closeDependents();
        resetDependents();
        console.log(res);
        $("#spaFieldsInput").val(res.data.fieldData);
        spaToast({
          status: "success",
          title: "Success",
          message: "Dependents saved successfully!",
        });
      } else {
        spaToast({
          status: "error",
          title: "Error",
          message: "Something went wrong!",
        });
      }
    },
    error: function (err) {
      spaToast({
        status: "error",
        title: "Error",
        message: err.responseText,
      });
    },
  });
});

$(document).on("click", ".spaAddChildOption", function () {
  const childRow = `<div class="spaChildOptionRow">
                            <div class="spaChildOptionColumn">
                                <label for="" class="spaLabel">Image</label>
                                <img class="spaChildOptionImage" src="" />
                                <input type="hidden" value="" class="spaChildOptionImageUrl">
                                <div class="spaChildOptionImageButtons">
                                    <button type="button" class="spaChildUploadImage spaSmallButton">Upload</button>
                                    <button type="button" class="spaChildDeleteImage spaSmallButton">Remove</button>
                                </div>
                            </div>
                            <div class="spaChildOptionColumn spaChildOptionColumnLabel">
                                <label for="" class="spaLabel">Label</label>
                                <input type="text" class="spaTextField spaSelectOptionValue">
                            </div>
                            <div class="spaChildOptionColumn">
                                <div class="spaFlex spaAlignCenter spaGap-1">
                                    <button type="button" class="spaButtonIcon spaDeleteChildRow"><i class="ri-delete-bin-fill"></i></button>
                                </div>
                            </div>
                        </div>`;
  $(this).closest(".spaChildOptionRows").append(childRow);
});

$(document).ready(function () {
  loadFields();
  $(".ruleFields").sortable({
    handle: ".spaFieldDrag",
    containment: ".ruleFields",
    axis: "y",
    tolerance: "pointer",
    update: function () {
      $(".spaProgressLoader").addClass("active");
      let order = [];

      $(".ruleFields .spaSingleField").each(function () {
        order.push($(this).data("id"));
      });

      $.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        data: {
          action: "reorder_serialized_array",
          order: order,
          serialized: $("#spaFieldsInput").val(),
        },
        success: function (res) {
          if (res.success) {
            $("#spaFieldsInput").val(res.data.serialized);
          }
          $(".spaProgressLoader").removeClass("active");
        },
      });
    },
  });

  const spaProductsSelect = $("#spaProductsSelect");
  const spaCategoriesSelect = $("#spaCategoriesSelect");
  const spaDependentsSelectShow = $("#spaShowIfChecked");
  const spaDependentsSelectHide = $("#spaHideIfChecked");

  spaProductsSelect.select2({
    width: "100%",
    placeholder: "Select products",
  });

  spaCategoriesSelect.select2({
    width: "100%",
    placeholder: "Select categories",
  });

  spaDependentsSelectShow.select2({
    width: "100%",
    placeholder: "Select options",
  });

  spaDependentsSelectHide.select2({
    width: "100%",
    placeholder: "Select options",
  });

  $('input[name="spaConditionType"]').on("change", function () {
    if (this.value === "products") {
      $("#spaProductsField").show();
      $("#spaCategoriesField").hide();
    } else {
      $("#spaProductsField").hide();
      $("#spaCategoriesField").show();
    }
  });
});
