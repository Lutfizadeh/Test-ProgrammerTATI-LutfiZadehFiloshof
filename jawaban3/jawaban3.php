<?php
$hasil_kerja;
$perilaku;

function predikat_kinerja($hasil_kerja, $perilaku) {
  $predikat = [
    "di bawah ekspektasi",
    "sesuai ekspektasi",
    "di atas ekspektasi"
  ];

  echo "\$hasil_kerja = '" . $hasil_kerja . "'\n";
  echo "\$perilaku = '" . $perilaku . "'\n";

  if ($hasil_kerja === $predikat[0] && $perilaku === $predikat[0]) return "Sangat Kurang";
  else if ($hasil_kerja === $predikat[2] && $perilaku === $predikat[2]) return "Sangat Baik";
  else if (($hasil_kerja === $predikat[1] || $hasil_kerja === $predikat[2]) && $perilaku === $predikat[0]) return "Kurang/misconduct";
  else if ($hasil_kerja === $predikat[0] && ($perilaku === $predikat[1] || $perilaku === $predikat[2])) return "Butuh perbaikan";
  else return "Baik";
}

$result = predikat_kinerja("di atas ekspektasi", "di atas ekspektasi");

echo "Output predikat kinerja " . $result;
?>
