// Full Screen
addEventListener("DOMContentLoaded", function () {
  const modelWrapper = document.querySelectorAll(".bp_model_parent");
  Object.values(modelWrapper).map((wrapper) => {
    const fullscreen = wrapper.querySelector("#openBtn");
    const closeBtn = wrapper.querySelector("#closeBtn");
    fullscreen.onclick = () => {
      wrapper.classList.add("fullscreen");
    };
    closeBtn.onclick = () => {
      wrapper.classList.remove("fullscreen");
    };
  });

  let carouselData = document.querySelectorAll(".bp3dmodel-carousel");
  Object.values(carouselData).map((itemData) => {
    if (itemData.dataset.fullscreen == 1) {
      createFullScreenFeature();
    }
  });
});

jQuery(document).ready(function ($) {
  // $(".bp3d-product-image").slick({
  //   slidesToShow: 1,
  //   slidesToScroll: 1,
  //   arrows: true,
  //   //fade: true,
  //   asNavFor: ".bp3dmodel-thumbnail",
  // });
  $(".bp3dmodel-carousel").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    //fade: true,
    //asNavFor: ".bp3d-product-image",
  });
});
function createFullScreenFeature() {
  const bigModel = document.querySelectorAll(".bp_model_gallery");

  const hideElement = document.createElement("div");
  Object.values(bigModel).map((bigModel) => {
    const item = bigModel.querySelector("model-viewer");

    const buttonWrapper = document.createElement("div");
    buttonWrapper.classList.add("bp3d-model-buttons");
    buttonWrapper.innerHTML =
      '<svg id="openBtn" width="24px" height="24px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="#f2f2f2" class="bi bi-arrows-fullscreen"> <path fill-rule="evenodd" d="M5.828 10.172a.5.5 0 0 0-.707 0l-4.096 4.096V11.5a.5.5 0 0 0-1 0v3.975a.5.5 0 0 0 .5.5H4.5a.5.5 0 0 0 0-1H1.732l4.096-4.096a.5.5 0 0 0 0-.707zm4.344 0a.5.5 0 0 1 .707 0l4.096 4.096V11.5a.5.5 0 1 1 1 0v3.975a.5.5 0 0 1-.5.5H11.5a.5.5 0 0 1 0-1h2.768l-4.096-4.096a.5.5 0 0 1 0-.707zm0-4.344a.5.5 0 0 0 .707 0l4.096-4.096V4.5a.5.5 0 1 0 1 0V.525a.5.5 0 0 0-.5-.5H11.5a.5.5 0 0 0 0 1h2.768l-4.096 4.096a.5.5 0 0 0 0 .707zm-4.344 0a.5.5 0 0 1-.707 0L1.025 1.732V4.5a.5.5 0 0 1-1 0V.525a.5.5 0 0 1 .5-.5H4.5a.5.5 0 0 1 0 1H1.732l4.096 4.096a.5.5 0 0 1 0 .707z"/> </svg> <svg id="closeBtn" width="34px" height="34px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path fill="none" stroke="#f2f2f2" stroke-width="2" d="M7,7 L17,17 M7,17 L17,7"/> </svg>';

    item.parentNode.appendChild(buttonWrapper);

    const newItem = item.parentNode.cloneNode(true);
    newItem.style.display = "none";
    newItem.onclick = function (e) {
      e.preventDefault();
    };
    const model = newItem.querySelector("model-viewer");
    model.style.width = "100%";
    model.style.height = "100%";
    model.style.maxHeight = "100%";

    hideElement.appendChild(newItem);

    const fullscreen = buttonWrapper.querySelector("#openBtn");
    const closefullscreen = newItem.querySelector("#closeBtn");
    fullscreen.onclick = () => {
      newItem.classList.add("fullscreen");
      newItem.style.display = "block";
    };
    closefullscreen.onclick = (e) => {
      e.preventDefault();
      newItem.classList.remove("fullscreen");
      newItem.style.display = "none";
    };
  });

  const body = document.querySelector("body");
  body.appendChild(hideElement);
}
