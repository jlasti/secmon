<table class="striped">
    <thead>
        <tr>
            <th>Datetime</th>
            <th>Host</th>
            <th>Protocol</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($filteredData as $data): ?>
        <tr>
            <td><?= $data->datetime ?></td>
            <td><?= $data->host ?></td>
            <td><?= $data->protocol ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
