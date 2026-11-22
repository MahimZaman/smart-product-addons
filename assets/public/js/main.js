(function ($) {
  "use strict";

  let SPA_BASE_PRICE = 0;

  function wcPrice(amount) {
    amount = parseFloat(amount || 0);

    const {
      currency_symbol,
      currency_position,
      decimal_separator,
      thousand_separator,
      decimals,
    } = spa_wc_price;

    let price = amount.toFixed(decimals);

    // Thousand separator
    price = price.replace(/\B(?=(\d{3})+(?!\d))/g, thousand_separator);

    // Decimal separator
    if (decimal_separator !== ".") {
      price = price.replace(".", decimal_separator);
    }

    switch (currency_position) {
      case "left":
        return currency_symbol + price;
      case "left_space":
        return currency_symbol + " " + price;
      case "right":
        return price + currency_symbol;
      case "right_space":
        return price + " " + currency_symbol;
      default:
        return currency_symbol + price;
    }
  }

  function spa_init_base_price() {
    if (!SPA_BASE_PRICE) {
      SPA_BASE_PRICE = $("#spaProductOptions").data("base") || 0;
    }
  }

  function spa_collect_addons() {
    let addons = [];
    let total = 0;

    /* ================================
     * Radios & Checkboxes
     * ================================ */
    $("#spaProductOptions")
      .find('input[type="radio"]:checked, input[type="checkbox"]:checked')
      .each(function () {
        const price = parseFloat($(this).data("price"));
        const label = $(this)
          .closest("label")
          .find(".spaCheckboxLabelText, .spaImageFieldToolTip")
          .first()
          .text()
          .trim();

        if (!label || label == "None") return;

        const addonPrice = isNaN(price) ? 0 : price;

        addons.push({
          label,
          price: addonPrice,
        });

        total += addonPrice;
      });

    /* ================================
     * Select fields
     * ================================ */
    $("#spaProductOptions select").each(function () {
      const $opt = $(this).find("option:selected");

      // Skip placeholders
      if (!$opt.val()) return;

      const label = $opt.text().replace(/\+.*/, "").trim();
      const match = $opt.data("price");
      const price = match ? parseFloat(match) : 0;

      addons.push({
        label,
        price,
      });

      total += price;
    });

    /* ================================
     * Text & Textarea
     * ================================ */
    $(
      '#spaProductOptions input[type="text"], #spaProductOptions textarea'
    ).each(function () {
      const val = $(this).val().trim();
      const label = $(this).data("label");
      if (!val) return;

      const price = parseFloat($(this).data("price"));
      const addonPrice = isNaN(price) ? 0 : price;

      addons.push({
        label: "" + label + " - " + val,
        price: addonPrice,
      });

      total += addonPrice;
    });

    return { addons, total };
  }

  function spa_render_price_table() {
    spa_init_base_price();

    const data = spa_collect_addons();
    const totalPrice = SPA_BASE_PRICE + data.total;
    const $tbody = $("#spaPriceReview tbody");

    $tbody.empty();

    // Base price
    $tbody.append(`
    <tr>
      <td>Base price</td>
      <td style="text-align:right; white-space:nowrap">
        ${wcPrice(SPA_BASE_PRICE)}
      </td>
    </tr>
  `);

    // Addons
    data.addons.forEach((addon) => {
      $tbody.append(`
      <tr>
        <td>${addon.label}</td>
        <td style="text-align:right; white-space:nowrap">
           ${addon.price == 0 ? "--" : "+ " + wcPrice(addon.price)}
        </td>
      </tr>
    `);
    });

    // Total
    $tbody.append(`
    <tr class="spaTotalRow">
      <td><strong>Total</strong></td>
      <td style="text-align:right; white-space:nowrap">
        <strong>${wcPrice(totalPrice)}</strong>
      </td>
    </tr>
  `);

    // Update Woo price display (visual only)
    $(".summary .price bdi").first().html(wcPrice(totalPrice));

    // Store addons for backend
    $("#spa_addons_data").val(JSON.stringify(data.addons));
  }

  function spa_is_empty(val) {
    if (val === null || val === undefined) return true;

    if (Array.isArray(val)) {
      return val.length === 0 || val.includes("None");
    }

    return val === "" || val === "None";
  }

  function spa_get_field_value(fieldName) {
    const $fields = $(`[name="${fieldName}"]`);

    if (!$fields.length) return null;

    if ($fields.is(":radio")) {
      return $fields.filter(":checked").val() || "";
    }

    if ($fields.is(":checkbox")) {
      return $fields
        .filter(":checked")
        .map(function () {
          return $(this).val();
        })
        .get();
    }

    return $fields.val();
  }

  function spa_match_condition(fieldValue, compare, expected) {
    switch (compare) {
      case "not-empty":
        return !spa_is_empty(fieldValue);

      case "empty":
        return spa_is_empty(fieldValue);

      case "equal":
        if (Array.isArray(fieldValue)) {
          return fieldValue.includes(expected);
        }
        return fieldValue == expected;

      case "not-equal":
        if (Array.isArray(fieldValue)) {
          return !fieldValue.includes(expected);
        }
        return fieldValue != expected;

      default:
        return false;
    }
  }

  function spa_evaluate_conditions($group) {
    const conditions = $group.data("condition");
    const relation = ($group.data("relation") || "AND").toUpperCase();

    if (!Array.isArray(conditions) || !conditions.length) {
      return true;
    }

    const results = conditions.map((condition) => {
      const fieldValue = spa_get_field_value(condition.field);
      return spa_match_condition(
        fieldValue,
        condition.compare,
        condition.value
      );
    });

    return relation === "OR" ? results.some(Boolean) : results.every(Boolean);
  }

  function spa_apply_conditional_logic() {
    $(".spaFieldGroup").each(function () {
      const $group = $(this);
      const show = spa_evaluate_conditions($group);

      $group.toggle(show);
    });
  }

  $(document).ready(function () {
    spa_init_base_price();

    if (!$("#spa_addons_data").length) {
      $("<input>", {
        type: "hidden",
        id: "spa_addons_data",
        name: "spa_addons_data",
      }).appendTo("form.cart");
    }

    // if (!$("#spaPriceReview").length) {
    //   $(
    //     '<div id="spaPriceReview"><table><tbody></tbody></table></div>'
    //   ).insertAfter("form.cart");
    // }

    $(document).on(
      "change input",
      "#spaProductOptions input, #spaProductOptions select, #spaProductOptions textarea",
      spa_render_price_table
    );

    spa_render_price_table();

    spa_apply_conditional_logic();

    $(document)
      .on(
        "change input",
        "#spaProductOptions input, #spaProductOptions select, #spaProductOptions textarea",
        spa_apply_conditional_logic
      )
      .on("change", ".spaCheckboxField input", function () {
        const dependents = $(this).data("dependents");
        if (typeof dependents == "object") {
          const showIfChecked = dependents.showIfChecked;
          const hideIfChecked = dependents.hideIfChecked;

          if (showIfChecked) {
            Array.from(showIfChecked).forEach((item, index) => {
              //const $target = $(this)
              const $target = $(this)
                //.closest(".spaFieldGroup")
                .closest("#spaProductOptions")
                .find(`input[value="${item}"]`);
              if ($(this).prop("checked")) {
                if ($target.prop("checked")) {
                  $target
                    .prop("checked", false)
                    .change()
                    .parent()
                    .removeClass("disabled");
                } else {
                  $target
                    .prop("checked", false)
                    .parent()
                    .removeClass("disabled");
                }
              } else {
                if ($target.prop("checked")) {
                  $target
                    .prop("checked", false)
                    .change()
                    .parent()
                    .addClass("disabled");
                } else {
                  $target.prop("checked", false).parent().addClass("disabled");
                }
              }
            });
          }

          if (hideIfChecked) {
            Array.from(hideIfChecked).forEach((item, index) => {
              //const $target = $(this)
              const $target = $(this)
                //.closest(".spaFieldGroup")
                .closest("#spaProductOptions")
                .find(`input[value="${item}"]`);
              if ($(this).prop("checked")) {
                if ($target.prop("checked")) {
                  $target
                    .prop("checked", false)
                    .change()
                    .parent()
                    .addClass("disabled");
                } else {
                  $target.prop("checked", false).parent().addClass("disabled");
                }
              } else {
                if ($target.prop("checked")) {
                  $target
                    .prop("checked", false)
                    .change()
                    .parent()
                    .removeClass("disabled");
                } else {
                  $target
                    .prop("checked", false)
                    .parent()
                    .removeClass("disabled");
                }
              }
            });
          }
        }
      });

    $(".spaCheckboxField input").each(function (index) {
      const dependents = $(this).data("dependents");
      if (typeof dependents == "object") {
        const showIfChecked = dependents.showIfChecked;
        const hideIfChecked = dependents.hideIfChecked;

        if (showIfChecked) {
          Array.from(showIfChecked).forEach((item, index) => {
            $(this)
              //.closest(".spaFieldGroup")
              .closest("#spaProductOptions")
              .find(`input[value="${item}"]`)
              .prop("checked", false)
              .change()
              .parent()
              .addClass("disabled");
          });
        }

        if (hideIfChecked) {
          Array.from(hideIfChecked).forEach((item, index) => {
            $(this)
              //.closest(".spaFieldGroup")
              .closest("#spaProductOptions")
              .find(`input[value="${item}"]`)
              .parent()
              .removeClass("disabled");
          });
        }
      }
    });
  });
})(jQuery);
