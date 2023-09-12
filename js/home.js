let ind = 0;
function changeText() {
  var x = document.getElementById("book-change");
  var services = ["Hotel", "Cabin", "Motel", "Resort", "Apartment", "Anything"];
  if (ind == services.length - 1) {
    x.style.color = "#f54242";
  } else {
    x.style.color = "#e39c9c";
  }
  x.innerHTML = services[i];
  ind++;
  if (ind >= services.length) {
    i = 0;
  }
  setTimeout(changeText, 2000);
}

function main() {
  changeText();
}
