<template>
  <div class="card collapse-icon">
    <div class="card-header">
      <h4 class="card-title">Global Settings</h4>
    </div>
    <div class="card-body">
      <div class="collapse-margin" id="accordionExample">
        <div class="card">
          <div class="card-header" id="headingOne" data-toggle="collapse" role="button" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <span class="lead collapse-title"> MFS Commission and Charges </span>
          </div>
          <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>MFS Name</th>
                      <th>Commission (%)</th>
                      <th>Charge (%)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>bKash</td>
                      <td><input type="text" class="form-control" placeholder="Commission (%)"></td>
                      <td><input type="text" class="form-control" placeholder="Charge (%)"></td>
                    </tr>
                    <tr>
                      <td>Nagad</td>
                      <td><input type="text" class="form-control" placeholder="Commission (%)"></td>
                      <td><input type="text" class="form-control" placeholder="Charge (%)"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <br>
              <button class="btn btn-danger waves-effect waves-float waves-light float-right" type="submit">Save</button>
              <br>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header" id="headingTwo" data-toggle="collapse" role="button" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
            <span class="lead collapse-title"> Currency Conversion Rate </span>
          </div>
          <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
            <div class="card-body">
              Sweet pie candy jelly. Sesame snaps biscuit sugar plum. Sweet roll topping fruitcake. Caramels
              liquorice biscuit ice cream fruitcake cotton candy tart. Donut caramels gingerbread jelly-o
              gingerbread pudding. Gummi bears pastry marshmallow candy canes pie. Pie apple pie carrot cake.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "settings"
}
</script>
