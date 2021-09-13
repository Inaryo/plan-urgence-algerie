let plus = document.getElementsByClassName("plus-button")
let moins = document.getElementsByClassName("minus-button")
let div;
let number;
for (let i = 0; i < plus.length; i = i + 1) {
    plus[i].addEventListener("click", () => {
        div = plus[i].nextElementSibling

        number = parseInt(div.textContent)
        number += 1

        number = number.toString()
        div.textContent = '0' + number;

    });
    moins[i].addEventListener("click", () => {
        div = plus[i].nextElementSibling
        number = parseInt(div.textContent)
        if (number > 0) {
            number -= 1
            number = number.toString()
            div.textContent = '0' + number;
        }

    });


}