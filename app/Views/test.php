<?= form_open(base_url('test/postreq')) ?>
    <input type="text" name="text1" placeholder="Text1"><br>
    <button type="submit" name="text1_btn">Submit Text1</button>
</form>

<?= form_open(base_url('test/postreq')) ?>
    <input type="text" name="text2" placeholder="Text2"><br>
    <button type="submit" name="text2_btn">Submit Text2</button>
</form>

<input type="checkbox" <?= $isChecked ? 'checked' : '' ?>>

<p>WRITEPATH <?= WRITEPATH ?></p>
<p>ROOTPATH <?= ROOTPATH ?></p>
<p>FCPATH <?= FCPATH ?></p>
<p>SYSTEMPATH <?= SYSTEMPATH ?></p>
<p>APPPATH <?= APPPATH ?></p>