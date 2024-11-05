import { createSlice, PayloadAction } from '@reduxjs/toolkit';

interface AuthState {
    isAuth: boolean;
    isAuthInProgress: boolean;
}

const initialState: AuthState = {
    isAuth: false,
    isAuthInProgress: true,
};

const authSlice = createSlice({
    name: 'auth',
    initialState,
    reducers: {
        setAuthStatus: (state, action: PayloadAction<boolean>) => {
            state.isAuth = action.payload;
        },
        setAuthInProgress: (state, action: PayloadAction<boolean>) => {
            state.isAuthInProgress = action.payload;
        },
    },
});

export const { setAuthStatus, setAuthInProgress } = authSlice.actions;
export default authSlice.reducer;
