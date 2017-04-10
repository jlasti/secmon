<table class="striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Timestamp</th>
            <th>TypeID</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($filteredData as $data): ?>
        <tr>
            <td><?= $data->id ?></td>
            <td><?= $data->title ?></td>
            <td><?= $data->timestamp ?></td>
            <td><?= $data->type_id ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
