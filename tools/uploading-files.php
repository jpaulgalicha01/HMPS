  <div>
      <div class="upload-box" onclick="document.getElementById('fileInput').click()">
          <div class="upload-icon">&#8679;</div>
          <div class="upload-text">
              Drop your files here<br>
              or click to browse from your device
          </div>
          <button class="upload-btn">Browse Files</button>
      </div>
      <input name="fileInput" type="file" id="fileInput" accept=".csv" required="true" />

      <div class="file-list" id="fileList"></div>
  </div>