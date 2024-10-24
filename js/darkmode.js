const nightModeToggle = document.getElementById('nightModeToggle');

if (localStorage.getItem('darkMode') === 'true') {
  document.body.classList.add('dark-mode');
  document.querySelectorAll('.nav-bar, .sakumssection, .jaunumisection, .kapecmussection, .sadarbibassection').forEach(section => {
    section.classList.add('dark-mode');
  });
  document.querySelectorAll('.button').forEach(button => {
    button.classList.add('dark-mode');
  });
  document.querySelectorAll('a').forEach(link => {
    link.classList.add('dark-mode');
  });
}

nightModeToggle.addEventListener('click', (event) => {
  event.preventDefault(); // Prevent the default anchor behavior
  document.body.classList.toggle('dark-mode');
  document.querySelectorAll('.nav-bar, .sakumssection, .jaunumisection, .kapecmussection, .sadarbibassection').forEach(section => {
    section.classList.toggle('dark-mode');
  });
  document.querySelectorAll('.button').forEach(button => {
    button.classList.toggle('dark-mode');
  });
  document.querySelectorAll('a').forEach(link => {
    link.classList.toggle('dark-mode');
  });

  // Save the current mode to localStorage
  localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
});
