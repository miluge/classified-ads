var content = document.getElementById('description'),
button = document.getElementById('print');

function generatePDF(){
  var doc = new jsPDF();

  doc.setFontSize(14);
  doc.text(25, 25, "Paranyan loves jsPDF");
//   doc.text(20, 20, content);
  doc.save('my.pdf');
}

button.addEventListener('click', generatePDF);