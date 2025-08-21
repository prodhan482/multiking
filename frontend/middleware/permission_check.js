export default function ({ route, redirect }) {

  if((typeof localStorage !== 'undefined'))
  {
    if(route.fullPath === "/users" && !JSON.parse(localStorage.userInfo).permission_lists.includes("UserController::list"))
      return redirect('/manage');

    if(route.fullPath === "/store" && !JSON.parse(localStorage.userInfo).permission_lists.includes("StoreController::list"))
      return redirect('/manage');

    if(route.fullPath === "/store/add" && !JSON.parse(localStorage.userInfo).permission_lists.includes("StoreController::create"))
      return redirect('/manage');

    if(route.fullPath === "/vendor" && !JSON.parse(localStorage.userInfo).permission_lists.includes("VendorController::list"))
      return redirect('/manage');

    if(route.fullPath === "/recharge" && !JSON.parse(localStorage.userInfo).permission_lists.includes("RechargeController::list"))
      return redirect('/manage');

    if(route.fullPath === "/mfs" && !JSON.parse(localStorage.userInfo).permission_lists.includes("MFSController::list"))
      return redirect('/manage');

    if(route.fullPath === "/promotion" && !JSON.parse(localStorage.userInfo).permission_lists.includes("PromotionController::list"))
      return redirect('/manage');

    if(route.fullPath.includes('/reports/') && (!JSON.parse(localStorage.userInfo).permission_lists.includes('ReportController::vendor_adjustment') || !JSON.parse(localStorage.userInfo).permission_lists.includes('ReportController::store_adjustment') || !JSON.parse(localStorage.userInfo).permission_lists.includes('ReportController::transaction')))
    {
      if((JSON.parse(localStorage.userInfo).user_type === "store") && (route.fullPath.includes('/reports/store_recharge_report')))
      {
        return "";
      }

      if((JSON.parse(localStorage.userInfo).user_type === "vendor") && (route.fullPath.includes('/reports/vendor_recharge_report')))
      {
        return "";
      }
      return redirect('/manage');
    }
  }

  return "";
}
