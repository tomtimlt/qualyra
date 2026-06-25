// ============================================================
// Qualyra vitrine — toggle thème (vanilla)
// ============================================================

(function () {
  var btn = document.getElementById('theme-toggle');
  if (!btn) return;
  function sync() {
    var cur = document.documentElement.dataset.theme || 'dark';
    btn.querySelector('.icon-sun').style.display  = (cur === 'dark')  ? '' : 'none';
    btn.querySelector('.icon-moon').style.display = (cur === 'light') ? '' : 'none';
  }
  sync();
  btn.addEventListener('click', function () {
    var next = (document.documentElement.dataset.theme === 'light') ? 'dark' : 'light';
    document.documentElement.dataset.theme = next;
    localStorage.setItem('qualyra-theme', next);
    sync();
  });
})();
