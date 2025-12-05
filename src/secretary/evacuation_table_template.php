<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Family Name</th>
        <th>Total Members</th>
        <th>Arrived / Total</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      if ($resultFamilies->num_rows > 0) {
        while ($row = $resultFamilies->fetch_assoc()) {
          $arrived = $row['arrived_count'] ? $row['arrived_count'] : 0;
          $total = $row['total_members'];
          
          // Color logic: Green if everyone arrived, Red if 0, Orange if partial
          $status_color = 'text-danger';
          if($arrived == $total) { $status_color = 'text-success'; }
          elseif($arrived > 0) { $status_color = 'text-warning'; }

          echo '<tr>
                  <td class="font-weight-bold">' . htmlspecialchars(ucfirst($row['last_name'])) . ' Family</td>
                  <td>' . $total . '</td>
                  <td class="'.$status_color.' status-fraction">' . $arrived . ' / ' . $total . '</td>
                  <td>
                    <button class="btn btn-info btn-sm view-family-btn" 
                      data-surname="'.htmlspecialchars($row['last_name']).'">
                      <i class="fas fa-eye"></i> Checklist
                    </button>
                  </td>
                </tr>';
        }
      } else {
        echo '<tr><td colspan="4" class="text-center">No families assigned to this center.</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>