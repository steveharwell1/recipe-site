<script>

	function message(msg, color = "black") {
			let flash = document.getElementById("flash");
			flash.innerHTML += `<div style="color: ${color};">${msg}</div>`;
	}
	function err(msg) {
		message(msg, 'red');
	}
	function clearMessage(){
		document.getElementById("flash").innerHTML = "";
	}
	var messages = <?php echo json_encode($messages) ?>;
	if(messages) messages.forEach((val) => message(val));
	var errors = <?php echo json_encode($errors) ?>;
	if(errors) errors.forEach((val) => err(val));

	let hamburger = document.getElementById("hamburger");
	let sidebar = document.getElementById("sidebar");
	hamburger.addEventListener('click', () => sidebar.classList.toggle('hide'));

</script>
</body>
</html>