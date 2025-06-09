<?php
// Uzsāk sesiju, lai varētu to iznīcināt
session_start();
// Iznīcina visas sesijas vērtības un beidz sesiju
session_destroy();
// Pāradresē lietotāju uz sākumlapu pēc izrakstīšanās
header('Location: index.html');
exit();
?>
