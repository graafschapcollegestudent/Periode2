<h1>Nieuwe taak</h1>

<form method="POST" action="/tasks">
    @csrf
    <input type="text" name="title" placeholder="Taak titel">
    <button type="submit">Opslaan</button>
</form>