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
        const token = localStorage.getItem("token");

        if (token) {
            // Token exists, so dispatch authenticated status
            this.dispatch(setAuthStatus(true));
            this.dispatch(setAuthInProgress(false));
        } else {
            // Token is missing, so attempt to refresh
            try {
                const resp = await AuthService.refreshToken();
                localStorage.setItem("token", resp.data.access_token);
                this.dispatch(setAuthStatus(true));
            } catch (error) {
                console.error("Token refresh failed:", error);
                // Optionally, set auth status to false if token refresh fails
                this.dispatch(setAuthStatus(false));
            } finally {
                this.dispatch(setAuthInProgress(false));
            }
        }
    }

    async logout(navigate: (to: string) => void) {
        this.dispatch(setAuthInProgress(true));

        try {
            await AuthService.logout();
            this.dispatch(setAuthStatus(false));
            localStorage.removeItem("token");
            navigate('/');
        } catch (err) {
            console.log("logout error");
        } finally {
            this.dispatch(setAuthInProgress(false));
        }
    }
}

export default (dispatch: AppDispatch) => new AuthStore(dispatch);
