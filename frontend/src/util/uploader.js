if (!XMLHttpRequest.prototype.sendAsBinary) {
  XMLHttpRequest.prototype.sendAsBinary = function(sData) {
    const nBytes = sData.length;
    const ui8Data = new Uint8Array(nBytes);
    for (let nIdx = 0; nIdx < nBytes; nIdx++) {
      ui8Data[nIdx] = sData.charCodeAt(nIdx) & 0xff;
    }
    this.send(ui8Data);
  };
}

export default function upload(fileData, url, token, checkTimeout = 300) {
  return new Promise(resolve => {
    const reader = new FileReader();
    let data;
    reader.onload = function() {
      data = reader.result;
    };
    reader.readAsBinaryString(fileData);

    const check = function() {
      if (typeof data !== 'undefined') {
        resolve({ data, attachment: fileData });
      }
      else {
        setTimeout(check, checkTimeout);
      }
    };
    check();
  }).then(data => {
    const fileName = data.attachment.name;
    const mime = data.attachment.type;
    const request = new XMLHttpRequest();
    const date = Date.now().toString(16);
    const boundary = `-------------------AdventureUploadBoundary-${date}`;
    const encoded = encodeURIComponent(fileName);
    const postUrl = `${url}?uploadType=multipart&filename=${encoded}`;
    request.open('POST', postUrl);
    request.setRequestHeader('Content-Type', `multipart/related; boundary=${boundary}`);
    request.setRequestHeader('Authorization', `Bearer ${token}`);

    return new Promise((resolve, reject) => {
      request.onload = function() {
        resolve(request.response);
      };
      request.onerror = function() {
        reject(request.response);
      };
      const header = `Content-Disposition: form-data; name="file"; filename="${fileName}"\r\nContent-Type: ${mime}`;
      const fileEntry = `--${boundary}\r\n${header}\r\n\r\n${data.data}\r\n`;
      const footer = `--${boundary}--\r\n`;
      request.sendAsBinary(`${fileEntry}${footer}`);
    });
  });
}
