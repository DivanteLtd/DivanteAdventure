function processRow(row) {
  const rowString = row
      .map(cell => (cell === null ? '' : cell.toString()))
      .map(cell => cell.replace(/"/g, '""'))
      .map(cell => (cell.search(/[",\n]/g) >= 0 ? `"${cell}"` : cell))
      .join(';');
  return `${rowString}\n`;
}

export default async function exportList(list, headers, fileName) {
  const rows = [];
  const headerRow = headers.map(header => header.label);
  rows.push(processRow(headerRow));
  list.forEach(element => {
    const row = headers.map(header => header.value(element));
    rows.push(processRow(row));
  });
  const csvContent = rows.join('');
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  if (navigator.msSaveBlob) {
    navigator.msSaveBlob(blob, fileName);
  }
  else {
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', fileName);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
}
