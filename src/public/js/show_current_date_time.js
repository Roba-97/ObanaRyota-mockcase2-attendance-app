function showCurrentDateTime() {
  const dayArray = [ "日", "月", "火", "水", "木", "金", "土" ] ;
  const now = new Date();

  const year = now.getFullYear();
  const month = now.getMonth() + 1;
  const date = now.getDate();
  const day = dayArray[now.getDay()];
  const today = `${year}年${month}月${date}日(${day})`;

  const hour = String(now.getHours()).padStart(2, '0');
  const minute = String(now.getMinutes()).padStart(2, '0');
  const time = `${hour}：${minute}`;

  document.getElementById('current_date').textContent = today;
  document.getElementById('current_time').textContent = time;
}

setInterval(showCurrentDateTime, 1000);