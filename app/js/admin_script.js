let header = document.querySelector('.header');

document.querySelector('#menu-btn').onclick = () => {
    header.classList.toggle('active');
}
window.onscroll = () => {
    header.classList.remove('active');
}