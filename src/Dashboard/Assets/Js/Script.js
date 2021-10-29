jQuery(document).ready(function () {
  const iframe = document.getElementById("badges-iframe");

  function authen() {
    jQuery.ajax({
      data: {
        action: "mskmbwp_getCodeAuth", //Tên action, dữ liệu gởi lên cho server
      },
      method: "POST",
      url: ajaxurl,
      success: function (response) {
        iframe.addEventListener("load", function () {
          iframe.contentWindow.postMessage(
            {
              payload: {
                url: window.MYSMBWP_GLOBAL.restBase,
                token: response.data.code,
                tidioId: window.MYSMBWP_GLOBAL.tidio || "",
                clientSite: window.MYSMBWP_GLOBAL.clientSite || "",
                email: window.MYSMBWP_GLOBAL.email || "",
                purchaseCode: window.MYSMBWP_GLOBAL.purchaseCode || "",
                purchaseCodeLink: window.MYSMBWP_GLOBAL.purchaseCodeLink || "",
                productName: window.MYSMBWP_GLOBAL.productName || "",
                endpointVerification:
                  window.MYSMBWP_GLOBAL.endpointVerification || "",
              },
              type: "@InitializePage/getWPInfoRequest",
            },
            "*"
          );
          iframe.classList.remove("hidden");
        });

        // check trường hợp login thành công
        if (iframe) {
          iframe.contentWindow.postMessage(
            {
              payload: {
                url: window.MYSMBWP_GLOBAL.restBase,
                token: response.data.code,
                tidioId: window.MYSMBWP_GLOBAL.tidio || "",
                clientSite: window.MYSMBWP_GLOBAL.clientSite || "",
                email: window.MYSMBWP_GLOBAL.email || "",
                purchaseCode: window.MYSMBWP_GLOBAL.purchaseCode || "",
                purchaseCodeLink: window.MYSMBWP_GLOBAL.purchaseCodeLink || "",
                productName: window.MYSMBWP_GLOBAL.productName || "",
                endpointVerification:
                  window.MYSMBWP_GLOBAL.endpointVerification || "",
              },
              type: "@InitializePage/getWPInfoRequest",
            },
            "*"
          );
        }
      },
      error: function (response) {
        iframe.addEventListener("load", function () {
          iframe.contentWindow.postMessage(
            {
              payload: {
                url: window.MYSMBWP_GLOBAL.restBase,
                token: "",
                tidioId: window.MYSMBWP_GLOBAL.tidio || "",
                clientSite: window.MYSMBWP_GLOBAL.clientSite || "",
                email: window.MYSMBWP_GLOBAL.email || "",
                purchaseCode: window.MYSMBWP_GLOBAL.purchaseCode || "",
                purchaseCodeLink: window.MYSMBWP_GLOBAL.purchaseCodeLink || "",
                productName: window.MYSMBWP_GLOBAL.productName || "",
                endpointVerification:
                  window.MYSMBWP_GLOBAL.endpointVerification || "",
              },
              type: "@InitializePage/getWPInfoRequest",
            },
            "*"
          );
          iframe.classList.remove("hidden");
        });
      },
    });
  }

  authen();

  window.addEventListener(
    "message",
    (event) => {
      if (event.data.type === "@HasPassed") {
        console.log(event.data);
        if (event.data.payload.hasPassed === true) {
          authen();
        }
      }
    },
    false
  );
  jQuery("#btn-Revoke-Purchase-Code").click(function () {
    let status = confirm("Are you sure you want to revoke the Purchase Code?");
    if (status) {
      jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: {
          action: "wookit_revokePurchaseCode",
          purchaseCode: MYSMBWP_GLOBAL.purchaseCode,
        },
        success: function (response) {
          location.reload();
        },
        error: function (jqXHR, error, errorThrown) {
          alert(jqXHR.responseJSON.message);
        },
      });
    }
  });
});
