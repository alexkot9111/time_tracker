import AuthService from "./ApiAuth.ts";
import { setAuthStatus, setAuthInProgress } from '../../store/authSlice';
import { AppDispatch } from '../../store/store';

class AuthStore {
    dispatch: AppDispatch;

    constructor(dispatch: AppDispatch) {
        this.dispatch = dispatch;
    }

    async login(email: string, password: string, navigate: (to: string) => void) {
        this.dispatch(setAuthInProgress(true));

        try {
            const resp = await AuthService.login(email, password);
            localStorage.setItem("token", resp.data.access_token);
            this.dispatch(setAuthStatus(true));
            navigate('/');
        } catch (err) {
            console.log("login error");
        } finally {
            this.dispatch(setAuthInProgress(false));
        }
    }

    async checkAuth() {
        this.dispatch(setAuthInProgress(true));

        try {
            const resp = await AuthService.refreshToken();
            localStorage.setItem("token", resp.data.access_token);
            this.dispatch(setAuthStatus(true));
        } catch (err) {
            console.log("login error");
        } finally {
            this.dispatch(setAuthInProgress(false));
        }
    }

    async logout() {
        this.dispatch(setAuthInProgress(true));

        try {
            await AuthService.logout();
            this.dispatch(setAuthStatus(false));
            localStorage.removeItem("token");
        } catch (err) {
            console.log("logout error");
        } finally {
            this.dispatch(setAuthInProgress(false));
        }
    }
}

export default (dispatch: AppDispatch) => new AuthStore(dispatch);
