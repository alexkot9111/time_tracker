import { Navigate, Outlet } from "react-router-dom";
import { observer } from "mobx-react-lite";
import { useSelector } from "react-redux";
import { RootState } from "../../store/store.ts";

const PrivateRoute = () => {

    const isAuth = useSelector((state: RootState) => state.auth.isAuth);
    const isAuthInProgress = useSelector((state: RootState) => state.auth.isAuthInProgress);

    if (isAuthInProgress) {
        return <div>Checking auth...</div>;
    }
    if (isAuth) {
        return <Outlet/>
    } else {
        return <Navigate to="/login" />;
    }
};

export default observer(PrivateRoute);