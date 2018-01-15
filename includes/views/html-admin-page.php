<div class="wrap">
    <h1>StayApp</h1>
    <form method="post" action="options.php">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="blogname">Token</label></th>
                    <td>
                        <input name="stay_settings[token]" type="text" id="token" value="" class="regular-text">
                        <input type="submit" name="submit" id="validationtoken" class="button button-primary" value="Validar" style="line-height: 25px;height: 25px;">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="PROMO">Promo</label>
                    </th>
                    <td>
                        <select name="stay_settings[promo]" id="WPLANG">
                            <?php foreach ($tickets as $key => $ticket): ?>
                                <option value=""><?= $ticket->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Habilitar</th>
                    <td>
                        <fieldset>
                            <label for="users_can_register">
                                <input name="stay_settings[enable]" type="checkbox" value="1">
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>