const atixSettings = window.wc.wcSettings.getSetting("atix_gateway_data", {});
const atixLabel =
  window.wp.htmlEntities.decodeEntities(atixSettings.title) ||
  window.wp.i18n.__("Atix Payment Services", "woocommerce-atix");
const atixContent = () => {
  return window.wp.htmlEntities.decodeEntities(atixSettings.description || "");
};

const IconPayment = ({ url, label }) => {
  return React.createElement("img", {
    style: { float: "right", marginRight: "20px", width: "120px" },
    src: url,
    alt: label,
  });
};

const CustomLabel = () => {
  return React.createElement(
    "div",
    { style: { width: "100%" } },
    React.createElement("span", { style: { width: "100%" } }, [
      atixLabel,
      IconPayment({ url: atixSettings.icon, label: atixLabel }),
    ])
  );
};

const Block_atix_Gateway = {
  name: "atix_gateway",
  label: Object(window.wp.element.createElement)(CustomLabel, null),
  content: Object(window.wp.element.createElement)(atixContent, null),
  edit: Object(window.wp.element.createElement)(atixContent, null),
  canMakePayment: () => true,
  ariaLabel: atixLabel,
  supports: {
    features: atixSettings.supports,
  },
};
window.wc.wcBlocksRegistry.registerPaymentMethod(Block_atix_Gateway);
