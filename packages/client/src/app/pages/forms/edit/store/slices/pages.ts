import type { PayloadAction } from '@reduxjs/toolkit';
import { createSlice } from '@reduxjs/toolkit';

import type { Page } from '../../builder/types/layout';
import type { SaveSubscriber } from '../middleware/state-persist';
import { TOPIC_SAVE } from '../middleware/state-persist';
import type { RootState } from '../store';

type PagesState = Page[];

type SwapPayload = {
  currentUid: string;
  targetUid: string;
};

const initialState: PagesState = [
  {
    uid: 'page-uid-1',
    label: 'Page One',
    handle: 'page-one',
    layoutUid: 'layout-uid-1',
    order: 1,
  },
];

export const pagesSlice = createSlice({
  name: 'pages',
  initialState,
  reducers: {
    add: (state, action: PayloadAction<Page>) => {
      state.push(action.payload);
    },
    remove: (state, action: PayloadAction<string>) => {
      state = state.filter((page) => page.uid !== action.payload);
    },
    swap: (state, action: PayloadAction<SwapPayload>) => {
      const current = state.find(
        (page) => page.uid === action.payload.currentUid
      );
      const target = state.find(
        (page) => page.uid === action.payload.targetUid
      );

      const tempOrder = current.order;
      current.order = target.order;
      target.order = tempOrder;
    },
  },
});

export const { swap, add, remove } = pagesSlice.actions;

export const selectPages = (state: RootState): Page[] => state.pages;

export const selectPage =
  (uid: string) =>
  (state: RootState): Page | undefined =>
    state.pages.find((page) => page.uid === uid);

export default pagesSlice.reducer;

const persist: SaveSubscriber = (_, data) => {
  const { state, persist } = data;

  persist.pages = state.pages;
};

PubSub.subscribe(TOPIC_SAVE, persist);
